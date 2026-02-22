package id.smkn1ciamis.amira;

import android.Manifest;
import android.app.Activity;
import android.app.AlertDialog;
import android.content.ActivityNotFoundException;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.graphics.Bitmap;
import android.net.ConnectivityManager;
import android.net.Network;
import android.net.NetworkCapabilities;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.os.Environment;
import android.os.Handler;
import android.os.Looper;
import android.provider.MediaStore;
import android.view.KeyEvent;
import android.view.View;
import android.view.WindowManager;
import android.webkit.CookieManager;
import android.webkit.ValueCallback;
import android.webkit.WebChromeClient;
import android.webkit.WebResourceError;
import android.webkit.WebResourceRequest;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import androidx.activity.result.ActivityResultLauncher;
import androidx.activity.result.contract.ActivityResultContracts;
import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.content.ContextCompat;
import androidx.core.content.FileProvider;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;

import java.io.File;
import java.io.IOException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Locale;

public class MainActivity extends AppCompatActivity {

    private WebView webView;
    private ProgressBar progressBar;
    private SwipeRefreshLayout swipeRefresh;
    private LinearLayout errorLayout;
    private TextView errorTitle;
    private TextView errorMessage;
    private Button retryButton;

    private ValueCallback<Uri[]> fileUploadCallback;
    private String cameraPhotoPath;

    private static final String BASE_URL = BuildConfig.BASE_URL;

    // ── Activity Result Launchers ──────────────────────────────────────

    private final ActivityResultLauncher<Intent> fileChooserLauncher =
            registerForActivityResult(
                    new ActivityResultContracts.StartActivityForResult(),
                    result -> {
                        if (fileUploadCallback == null) return;

                        Uri[] results = null;

                        if (result.getResultCode() == Activity.RESULT_OK && result.getData() != null) {
                            Intent data = result.getData();
                            String dataString = data.getDataString();

                            if (dataString != null) {
                                results = new Uri[]{Uri.parse(dataString)};
                            } else if (data.getClipData() != null) {
                                int count = data.getClipData().getItemCount();
                                results = new Uri[count];
                                for (int i = 0; i < count; i++) {
                                    results[i] = data.getClipData().getItemAt(i).getUri();
                                }
                            }
                        } else if (result.getResultCode() == Activity.RESULT_OK) {
                            // Camera result
                            if (cameraPhotoPath != null) {
                                results = new Uri[]{Uri.parse(cameraPhotoPath)};
                            }
                        }

                        fileUploadCallback.onReceiveValue(results);
                        fileUploadCallback = null;
                    }
            );

    private final ActivityResultLauncher<String> cameraPermissionLauncher =
            registerForActivityResult(
                    new ActivityResultContracts.RequestPermission(),
                    isGranted -> {
                        if (!isGranted) {
                            Toast.makeText(this, "Izin kamera diperlukan untuk mengambil foto", Toast.LENGTH_SHORT).show();
                        }
                    }
            );

    // ── Lifecycle ──────────────────────────────────────────────────────

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        // Fullscreen: hide status bar color blend
        getWindow().addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);
        getWindow().setStatusBarColor(ContextCompat.getColor(this, R.color.primary));

        initViews();
        setupSwipeRefresh();
        setupWebView();

        if (savedInstanceState != null) {
            webView.restoreState(savedInstanceState);
        } else {
            loadUrl(BASE_URL);
        }
    }

    @Override
    protected void onSaveInstanceState(@NonNull Bundle outState) {
        super.onSaveInstanceState(outState);
        webView.saveState(outState);
    }

    @Override
    protected void onResume() {
        super.onResume();
        webView.onResume();
    }

    @Override
    protected void onPause() {
        webView.onPause();
        super.onPause();
    }

    @Override
    protected void onDestroy() {
        webView.destroy();
        super.onDestroy();
    }

    // ── View Init ──────────────────────────────────────────────────────

    private void initViews() {
        webView = findViewById(R.id.webView);
        progressBar = findViewById(R.id.progressBar);
        swipeRefresh = findViewById(R.id.swipeRefresh);
        errorLayout = findViewById(R.id.errorLayout);
        errorTitle = findViewById(R.id.errorTitle);
        errorMessage = findViewById(R.id.errorMessage);
        retryButton = findViewById(R.id.retryButton);

        retryButton.setOnClickListener(v -> {
            hideError();
            loadUrl(webView.getUrl() != null ? webView.getUrl() : BASE_URL);
        });
    }

    private void setupSwipeRefresh() {
        swipeRefresh.setColorSchemeColors(
                ContextCompat.getColor(this, R.color.primary),
                ContextCompat.getColor(this, R.color.primary_dark)
        );
        swipeRefresh.setOnRefreshListener(() -> {
            if (webView.getUrl() != null) {
                webView.reload();
            } else {
                loadUrl(BASE_URL);
            }
        });
    }

    // ── WebView Setup ──────────────────────────────────────────────────

    private void setupWebView() {
        WebSettings settings = webView.getSettings();

        // JavaScript & DOM Storage
        settings.setJavaScriptEnabled(true);
        settings.setDomStorageEnabled(true);
        settings.setDatabaseEnabled(true);

        // Cache strategy
        if (isNetworkAvailable()) {
            settings.setCacheMode(WebSettings.LOAD_DEFAULT);
        } else {
            settings.setCacheMode(WebSettings.LOAD_CACHE_ELSE_NETWORK);
        }

        // Viewport & zoom
        settings.setUseWideViewPort(true);
        settings.setLoadWithOverviewMode(true);
        settings.setSupportZoom(false);
        settings.setBuiltInZoomControls(false);

        // File access
        settings.setAllowFileAccess(true);
        settings.setAllowContentAccess(true);

        // Media
        settings.setMediaPlaybackRequiresUserGesture(false);

        // Mixed content (for dev)
        settings.setMixedContentMode(WebSettings.MIXED_CONTENT_COMPATIBILITY_MODE);

        // User agent: append Calakan identifier
        String ua = settings.getUserAgentString();
        settings.setUserAgentString(ua + " Calakan-Android/1.0");

        // Cookies
        CookieManager cookieManager = CookieManager.getInstance();
        cookieManager.setAcceptCookie(true);
        cookieManager.setAcceptThirdPartyCookies(webView, true);

        // WebViewClient — handle navigation & errors
        webView.setWebViewClient(new WebViewClient() {

            @Override
            public boolean shouldOverrideUrlLoading(WebView view, WebResourceRequest request) {
                String url = request.getUrl().toString();

                // Keep internal URLs in WebView
                if (url.contains("ramadhan.smkn1ciamis.id") ||
                    url.contains("127.0.0.1") ||
                    url.contains("10.0.2.2") ||
                    url.startsWith("http://192.168.")) {
                    return false; // load in WebView
                }

                // External links → open in browser
                try {
                    Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse(url));
                    startActivity(intent);
                } catch (ActivityNotFoundException e) {
                    Toast.makeText(MainActivity.this, "Tidak dapat membuka link", Toast.LENGTH_SHORT).show();
                }
                return true;
            }

            @Override
            public void onPageStarted(WebView view, String url, Bitmap favicon) {
                super.onPageStarted(view, url, favicon);
                progressBar.setVisibility(View.VISIBLE);
                hideError();
            }

            @Override
            public void onPageFinished(WebView view, String url) {
                super.onPageFinished(view, url);
                progressBar.setVisibility(View.GONE);
                swipeRefresh.setRefreshing(false);

                // Inject CSS to hide browser-specific elements if needed
                injectCustomCSS(view);
            }

            @Override
            public void onReceivedError(WebView view, WebResourceRequest request, WebResourceError error) {
                // Only show error for main frame
                if (request.isForMainFrame()) {
                    progressBar.setVisibility(View.GONE);
                    swipeRefresh.setRefreshing(false);

                    if (!isNetworkAvailable()) {
                        showError("Tidak Ada Koneksi Internet",
                                "Periksa koneksi WiFi atau data seluler Anda, lalu coba lagi.");
                    } else {
                        showError("Gagal Memuat Halaman",
                                "Terjadi kesalahan saat memuat halaman. Silakan coba lagi.");
                    }
                }
            }
        });

        // WebChromeClient — handle file uploads & progress
        webView.setWebChromeClient(new WebChromeClient() {

            @Override
            public void onProgressChanged(WebView view, int newProgress) {
                progressBar.setProgress(newProgress);
                if (newProgress == 100) {
                    progressBar.setVisibility(View.GONE);
                }
            }

            @Override
            public boolean onShowFileChooser(WebView webView,
                                             ValueCallback<Uri[]> filePathCallback,
                                             FileChooserParams fileChooserParams) {
                // Cancel any previous callback
                if (fileUploadCallback != null) {
                    fileUploadCallback.onReceiveValue(null);
                }
                fileUploadCallback = filePathCallback;

                openFileChooser(fileChooserParams);
                return true;
            }
        });
    }

    // ── File Upload Handling ───────────────────────────────────────────

    private void openFileChooser(WebChromeClient.FileChooserParams params) {
        Intent intent = new Intent(Intent.ACTION_GET_CONTENT);
        intent.addCategory(Intent.CATEGORY_OPENABLE);
        intent.setType("*/*");

        String[] acceptTypes = params.getAcceptTypes();
        if (acceptTypes != null && acceptTypes.length > 0 && !acceptTypes[0].isEmpty()) {
            if (acceptTypes.length == 1) {
                intent.setType(acceptTypes[0]);
            } else {
                intent.setType("*/*");
                intent.putExtra(Intent.EXTRA_MIME_TYPES, acceptTypes);
            }
        }

        // Allow multiple file selection if requested
        if (params.getMode() == WebChromeClient.FileChooserParams.MODE_OPEN_MULTIPLE) {
            intent.putExtra(Intent.EXTRA_ALLOW_MULTIPLE, true);
        }

        // Check if camera capture is applicable (for image types)
        boolean isImageType = false;
        if (acceptTypes != null) {
            for (String type : acceptTypes) {
                if (type != null && type.startsWith("image/")) {
                    isImageType = true;
                    break;
                }
            }
        }

        Intent chooserIntent;
        if (isImageType && hasCameraPermission()) {
            Intent cameraIntent = createCameraIntent();
            if (cameraIntent != null) {
                chooserIntent = Intent.createChooser(intent, "Pilih file");
                chooserIntent.putExtra(Intent.EXTRA_INITIAL_INTENTS, new Intent[]{cameraIntent});
            } else {
                chooserIntent = Intent.createChooser(intent, "Pilih file");
            }
        } else {
            chooserIntent = Intent.createChooser(intent, "Pilih file");
        }

        try {
            fileChooserLauncher.launch(chooserIntent);
        } catch (Exception e) {
            fileUploadCallback.onReceiveValue(null);
            fileUploadCallback = null;
            Toast.makeText(this, "Tidak dapat membuka file picker", Toast.LENGTH_SHORT).show();
        }
    }

    private Intent createCameraIntent() {
        Intent cameraIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
        if (cameraIntent.resolveActivity(getPackageManager()) != null) {
            File photoFile = null;
            try {
                photoFile = createImageFile();
            } catch (IOException ignored) {}

            if (photoFile != null) {
                cameraPhotoPath = "file:" + photoFile.getAbsolutePath();
                Uri photoUri = FileProvider.getUriForFile(this,
                        getApplicationContext().getPackageName() + ".fileprovider",
                        photoFile);
                cameraIntent.putExtra(MediaStore.EXTRA_OUTPUT, photoUri);
                return cameraIntent;
            }
        }
        return null;
    }

    private File createImageFile() throws IOException {
        String timeStamp = new SimpleDateFormat("yyyyMMdd_HHmmss", Locale.getDefault()).format(new Date());
        String imageFileName = "Calakan_" + timeStamp + "_";
        File storageDir = getExternalFilesDir(Environment.DIRECTORY_PICTURES);
        return File.createTempFile(imageFileName, ".jpg", storageDir);
    }

    private boolean hasCameraPermission() {
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.CAMERA)
                != PackageManager.PERMISSION_GRANTED) {
            cameraPermissionLauncher.launch(Manifest.permission.CAMERA);
            return false;
        }
        return true;
    }

    // ── CSS Injection ──────────────────────────────────────────────────

    private void injectCustomCSS(WebView view) {
        // Hide any elements that shouldn't appear in the app
        String css = ""
                + "/* Sembunyikan scrollbar horizontal */"
                + "body { overflow-x: hidden !important; }"
                + "/* Prevent text selection highlight on long press */"
                + "-webkit-touch-callout: none;";

        String js = "javascript:(function() {"
                + "var style = document.createElement('style');"
                + "style.type = 'text/css';"
                + "style.innerHTML = '" + css + "';"
                + "document.head.appendChild(style);"
                + "})()";
        view.loadUrl(js);
    }

    // ── Error Handling UI ──────────────────────────────────────────────

    private void showError(String title, String message) {
        errorLayout.setVisibility(View.VISIBLE);
        webView.setVisibility(View.GONE);
        errorTitle.setText(title);
        errorMessage.setText(message);
    }

    private void hideError() {
        errorLayout.setVisibility(View.GONE);
        webView.setVisibility(View.VISIBLE);
    }

    // ── Network Check ──────────────────────────────────────────────────

    private boolean isNetworkAvailable() {
        ConnectivityManager cm = getSystemService(ConnectivityManager.class);
        if (cm == null) return false;
        Network network = cm.getActiveNetwork();
        if (network == null) return false;
        NetworkCapabilities caps = cm.getNetworkCapabilities(network);
        return caps != null && (
                caps.hasTransport(NetworkCapabilities.TRANSPORT_WIFI) ||
                caps.hasTransport(NetworkCapabilities.TRANSPORT_CELLULAR) ||
                caps.hasTransport(NetworkCapabilities.TRANSPORT_ETHERNET)
        );
    }

    // ── Navigation ─────────────────────────────────────────────────────

    private void loadUrl(String url) {
        hideError();
        if (isNetworkAvailable()) {
            webView.loadUrl(url);
        } else {
            showError("Tidak Ada Koneksi Internet",
                    "Periksa koneksi WiFi atau data seluler Anda, lalu coba lagi.");
        }
    }

    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        // Handle back button: go back in WebView history first
        if (keyCode == KeyEvent.KEYCODE_BACK && webView.canGoBack()) {
            webView.goBack();
            return true;
        }

        // If can't go back, show exit confirmation
        if (keyCode == KeyEvent.KEYCODE_BACK) {
            new AlertDialog.Builder(this)
                    .setTitle("Keluar Aplikasi")
                    .setMessage("Apakah Anda yakin ingin keluar dari Calakan?")
                    .setPositiveButton("Ya", (dialog, which) -> finish())
                    .setNegativeButton("Tidak", null)
                    .show();
            return true;
        }

        return super.onKeyDown(keyCode, event);
    }
}
