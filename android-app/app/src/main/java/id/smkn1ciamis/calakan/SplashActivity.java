package id.smkn1ciamis.calakan;

import android.annotation.SuppressLint;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.view.WindowManager;
import android.view.animation.AlphaAnimation;
import android.view.animation.Animation;
import android.widget.ImageView;
import android.widget.TextView;

import androidx.appcompat.app.AppCompatActivity;
import androidx.core.content.ContextCompat;

@SuppressLint("CustomSplashScreen")
public class SplashActivity extends AppCompatActivity {

    private static final long SPLASH_DELAY = 2000; // 2 detik

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_splash);

        // Fullscreen splash
        getWindow().addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);
        getWindow().setStatusBarColor(ContextCompat.getColor(this, R.color.primary));

        // Fade-in animation
        ImageView logo = findViewById(R.id.splashLogo);
        TextView appName = findViewById(R.id.splashAppName);
        TextView appFullName = findViewById(R.id.splashAppFullName);
        TextView schoolName = findViewById(R.id.splashSchoolName);

        AlphaAnimation fadeIn = new AlphaAnimation(0.0f, 1.0f);
        fadeIn.setDuration(800);
        fadeIn.setFillAfter(true);

        AlphaAnimation fadeInDelay = new AlphaAnimation(0.0f, 1.0f);
        fadeInDelay.setDuration(600);
        fadeInDelay.setStartOffset(400);
        fadeInDelay.setFillAfter(true);

        AlphaAnimation fadeInDelay2 = new AlphaAnimation(0.0f, 1.0f);
        fadeInDelay2.setDuration(600);
        fadeInDelay2.setStartOffset(700);
        fadeInDelay2.setFillAfter(true);

        logo.startAnimation(fadeIn);
        appName.startAnimation(fadeInDelay);
        appFullName.startAnimation(fadeInDelay2);
        schoolName.startAnimation(fadeInDelay2);

        // Navigate to MainActivity after delay
        new Handler(Looper.getMainLooper()).postDelayed(() -> {
            Intent intent = new Intent(SplashActivity.this, MainActivity.class);
            startActivity(intent);
            finish();
            overridePendingTransition(android.R.anim.fade_in, android.R.anim.fade_out);
        }, SPLASH_DELAY);
    }
}
