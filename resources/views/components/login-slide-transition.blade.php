{{-- Login page slide transition --}}
<style>
  @keyframes loginSlideOutLeft {
    to { opacity: 0; transform: translateX(-60px); }
  }
  @keyframes loginSlideInFromRight {
    from { opacity: 0; transform: translateX(60px); }
    to { opacity: 1; transform: translateX(0); }
  }
  @keyframes loginSlideOutRight {
    to { opacity: 0; transform: translateX(60px); }
  }
  @keyframes loginSlideInFromLeft {
    from { opacity: 0; transform: translateX(-60px); }
    to { opacity: 1; transform: translateX(0); }
  }
  .login-container.slide-out-left {
    animation: loginSlideOutLeft 0.3s ease-in forwards !important;
  }
  .login-container.slide-out-right {
    animation: loginSlideOutRight 0.3s ease-in forwards !important;
  }
  .login-container.slide-in-from-right {
    animation: loginSlideInFromRight 0.4s ease-out both !important;
  }
  .login-container.slide-in-from-left {
    animation: loginSlideInFromLeft 0.4s ease-out both !important;
  }
</style>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Define page order for slide direction
    var pageOrder = ['/siswa/login', '/portal-guru-smkn1/login', '/portal-kesiswaan-smkn1/login'];
    var currentPath = window.location.pathname;
    var currentIndex = pageOrder.indexOf(currentPath);

    // Check if we arrived from a slide transition
    var slideFrom = sessionStorage.getItem('login-slide-dir');
    if (slideFrom) {
      sessionStorage.removeItem('login-slide-dir');
      var container = document.querySelector('.login-container');
      if (container) {
        container.classList.add(slideFrom === 'left' ? 'slide-in-from-right' : 'slide-in-from-left');
      }
    }

    // Intercept portal link clicks
    document.querySelectorAll('a[href]').forEach(function(link) {
      var href = link.getAttribute('href');
      var targetPath = null;
      try {
        var url = new URL(href, window.location.origin);
        targetPath = url.pathname;
      } catch(e) { return; }

      var targetIndex = pageOrder.indexOf(targetPath);
      if (targetIndex === -1 || targetIndex === currentIndex) return;

      link.addEventListener('click', function(e) {
        e.preventDefault();
        var container = document.querySelector('.login-container');
        if (!container) { window.location.href = href; return; }

        // Determine slide direction: going right in order = slide left, vice versa
        var goingRight = targetIndex > currentIndex;
        sessionStorage.setItem('login-slide-dir', goingRight ? 'left' : 'right');
        container.classList.add(goingRight ? 'slide-out-left' : 'slide-out-right');

        setTimeout(function() { window.location.href = href; }, 280);
      });
    });
  });
</script>
