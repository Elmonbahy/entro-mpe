<button id="scrollToTopBtn" class="scroll-btn scroll-up" onclick="scrollToTop()"><i
    class="bi bi-arrow-up-circle-fill"></i></button>
<button id="scrollToBottomBtn" class="scroll-btn scroll-down" onclick="scrollToBottom()"><i
    class="bi bi-arrow-down-circle-fill"></i></button>

<style>
  .scroll-btn {
    position: fixed;
    right: 20px;
    z-index: 999;
    background-color: #5f9fff;
    color: #fff;
    border: none;
    padding: 10px 14px;
    border-radius: 50%;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
    cursor: pointer;
    display: none;
    transition: opacity 0.3s ease;
  }

  .scroll-up {
    bottom: 80px;
  }

  .scroll-down {
    bottom: 20px;
  }

  .scroll-btn:hover {
    background-color: #084298;
  }
</style>

<script>
  const scrollToTopBtn = document.getElementById('scrollToTopBtn');
  const scrollToBottomBtn = document.getElementById('scrollToBottomBtn');

  window.addEventListener('scroll', () => {
    const scrollTop = window.scrollY;
    const scrollBottom = document.body.scrollHeight - window.innerHeight - scrollTop;

    scrollToTopBtn.style.display = scrollTop > 100 ? 'block' : 'none';
    scrollToBottomBtn.style.display = scrollBottom > 100 ? 'block' : 'none';
  });

  function scrollToTop() {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  }

  function scrollToBottom() {
    window.scrollTo({
      top: document.body.scrollHeight,
      behavior: 'smooth'
    });
  }
</script>
