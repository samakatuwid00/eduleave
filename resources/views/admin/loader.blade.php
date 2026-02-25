<style>
  /* Loader styling */
  .loader {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100vh; /* Fullscreen loader */
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background-color: rgba(255, 255, 255, 0.8);
    z-index: 9999;
  }

  .loader img {
    width: 48px; /* Icon size */
    animation: moveIcon 1.5s ease-in-out infinite;
  }

  .loader .loading-text {
    margin-top: 16px; /* Space between icon and label */
    font-size: 18px;
    font-weight: bold;
    color: #333;
    font-family: Arial, sans-serif;
  }

  @keyframes moveIcon {
    0%, 100% {
      transform: translateX(0);
    }
    50% {
      transform: translateX(50px);
    }
  }
</style>

<div class="loader">
  <img src="{{ asset('assets/images/icons8-leave-48.png') }}" alt="Loading...">
  <div class="loading-text">Loading...</div>
</div>

<script>
  // Hide loader when the page has fully loaded
  window.addEventListener('load', () => {
    document.querySelector('.loader').style.display = 'none';
  });
</script>


<!-- <script>
  // Hide loader after 3 seconds (example timing)
  window.addEventListener('load', () => {
    setTimeout(() => {
      document.querySelector('.loader').style.display = 'none';
    }, 1000);
  });
</script> -->