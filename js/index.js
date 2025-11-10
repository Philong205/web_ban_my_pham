// Lấy các thành phần cần thiết
const slides = document.querySelector(".slides"); // Container chứa slide
const slideItems = document.querySelectorAll(".slide"); // Từng slide
const prevButton = document.querySelector(".prev"); // Nút quay lại
const nextButton = document.querySelector(".next"); // Nút tiếp theo
document.documentElement.style.setProperty("--total-slides", slideItems.length);

let currentIndex = 0; // Slide hiện tại
const totalSlides = slideItems.length; // Tổng số slide

// Hiển thị slide
function showSlide(index) {
  if (index >= totalSlides) {
    currentIndex = 0; // Quay lại slide đầu tiên
  } else if (index < 0) {
    currentIndex = totalSlides - 1; // Quay lại slide cuối cùng
  } else {
    currentIndex = index;
  }

  // Di chuyển slide
  const offset = -currentIndex * 100; // Tính toán vị trí
  slides.style.transform = `translateX(${offset}%)`;
}

// Tự động chuyển slide
let autoSlide = setInterval(() => {
  showSlide(currentIndex + 1);
}, 5000); // 5 giây

// Nút "Quay lại"
prevButton.addEventListener("click", () => {
  showSlide(currentIndex - 1);
  resetAutoSlide();
});

// Nút "Tiếp theo"
nextButton.addEventListener("click", () => {
  showSlide(currentIndex + 1);
  resetAutoSlide();
});

// Đặt lại bộ đếm tự động khi điều hướng thủ công
function resetAutoSlide() {
  clearInterval(autoSlide); // Dừng bộ đếm hiện tại
  autoSlide = setInterval(() => {
    showSlide(currentIndex + 1);
  }, 4000); // Bắt đầu lại
}

//responsive search box
document.querySelector(".search-btn").addEventListener("click", function () {
  var searchBox = document.querySelector(".search-box");
  searchBox.classList.toggle("active");
});

// JavaScript for toggling the mobile menu
const menuToggle = document.querySelector(".menu-toggle");
const mainMenu = document.querySelector(".display1");

// Toggle the mobile menu when hamburger is clicked
menuToggle.addEventListener("click", () => {
  mainMenu.classList.toggle("active");
});

// Toggle submenu visibility when clicking on a category
const menuItems = document.querySelectorAll(".menu-item.has-submenu");

menuItems.forEach((item) => {
  item.addEventListener("click", (e) => {
    // Prevent closing the menu when clicking inside it
    e.stopPropagation();
    item.classList.toggle("active");
  });
});

// Close menu when clicking outside of it
document.addEventListener("click", (e) => {
  if (!e.target.closest(".menu")) {
    mainMenu.classList.remove("active");
    menuItems.forEach((item) => item.classList.remove("active"));
  }
});
