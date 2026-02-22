# Calakan ProGuard Rules

# Keep WebView JS interface
-keepclassmembers class id.smkn1ciamis.calakan.MainActivity {
    public *;
}

# Keep WebViewClient and WebChromeClient
-keep class * extends android.webkit.WebViewClient
-keep class * extends android.webkit.WebChromeClient

# Keep JavaScript interface methods
-keepclassmembers class * {
    @android.webkit.JavascriptInterface <methods>;
}

# AndroidX
-keep class androidx.** { *; }
-dontwarn androidx.**
