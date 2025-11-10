// CART
// Thêm hàm xử lý giỏ hàng
document.addEventListener("DOMContentLoaded", function () {
  // Xử lý thay đổi số lượng
  document.querySelectorAll('input[type="number"]').forEach((input) => {
    input.addEventListener("change", function () {
      const productId = this.getAttribute("data-product-id");
      const quantity = this.value;
      updateQuantity(productId, quantity);
    });
  });

  // Xử lý nút xóa
  document.querySelectorAll(".delete-btn").forEach((button) => {
    button.addEventListener("click", function () {
      const productId = this.getAttribute("data-product-id");
      if (confirm("Bạn có chắc chắn muốn xóa sản phẩm này?")) {
        removeItem(productId);
      }
    });
  });
});

async function updateQuantity(productId, quantity) {
  try {
    const response = await fetch("../php/update_cart.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `action=update&productId=${productId}&quantity=${quantity}`,
    });

    const result = await response.json();
    if (result.success) {
      location.reload();
    } else {
      alert("Cập nhật thất bại: " + result.message);
    }
  } catch (error) {
    console.error("Error:", error);
    alert("Có lỗi xảy ra khi cập nhật giỏ hàng");
  }
}

//Xóa sản phẩm khỏi giỏ hàng
async function removeItem(productId) {
  try {
    const response = await fetch("../php/update_cart.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `action=remove&productId=${productId}`,
    });

    const result = await response.json();
    console.log("Server response:", result); // Xem phản hồi từ server trong console

    if (result.success) {
      // Hiển thị thông báo thành công
      // alert("Xóa sản phẩm thành công");

      // Tải lại trang giỏ hàng để cập nhật lại giỏ hàng
      location.reload(); // Tải lại trang
    } else {
      alert("Xóa thất bại: " + result.message);
    }
  } catch (error) {
    console.error("Error:", error);
  }
}

//CHECKOUT
document.addEventListener("DOMContentLoaded", function () {
  // Xử lý hiển thị địa chỉ
  const addressOptions = document.querySelectorAll(
    'input[name="shipping_address"]'
  );
  addressOptions.forEach((option) => {
    option.addEventListener("change", function () {
      updateSummaryDisplay();
    });
  });

  // Xử lý hiển thị phương thức thanh toán
  const paymentOptions = document.querySelectorAll(
    'input[name="payment_method"]'
  );
  paymentOptions.forEach((option) => {
    option.addEventListener("change", updateSummaryDisplay);
  });

  // Lưu lựa chọn vào sessionStorage
  document.querySelectorAll('input[name="payment_method"]').forEach((input) => {
    input.addEventListener("change", function () {
      sessionStorage.setItem("selectedPaymentMethod", this.value);
    });
  });

  document
    .querySelectorAll('input[name="shipping_address"]')
    .forEach((input) => {
      input.addEventListener("change", function () {
        sessionStorage.setItem("selectedAddress", this.value);
      });
    });
});

// Nút lưu dịa chỉ mới
document
  .getElementById("save-address-btn")
  ?.addEventListener("click", function () {
    const address = document
      .querySelector('textarea[name="new_address"]')
      .value.trim();

    if (!address) {
      alert("Vui lòng nhập địa chỉ");
      return;
    }

    fetch("../BackEnd/save_address.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `address=${encodeURIComponent(address)}`,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert("Đã lưu địa chỉ mới!");
          location.reload();
        } else {
          alert("Lỗi: " + data.message);
        }
      });
  });

//cập nhật hiển thị
function updateSummaryDisplay() {
  if (document.getElementById("step-3")) {
    // Hiển thị địa chỉ
    const selectedAddress = document.querySelector(
      'input[name="shipping_address"]:checked'
    );
    let addressHtml = "";

    if (selectedAddress.value.startsWith("saved_")) {
      const addressCard = selectedAddress.closest(".address-card");
      addressHtml = addressCard.querySelector(".address-details").innerHTML;
    } else {
      const address = document.querySelector(
        'textarea[name="new_address"]'
      ).value;
      addressHtml = `
                <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong><br>
                ${address}<br>
                ĐT: <?= htmlspecialchars($_SESSION['user']['SDT']) ?>
            `;
    }

    // Hiển thị phương thức thanh toán
    const paymentMethod = document.querySelector(
      'input[name="payment_method"]:checked'
    ).value;
    let paymentHtml = "";

    switch (paymentMethod) {
      case "Thanh toán khi nhận hàng.":
        paymentHtml =
          '<i class="fas fa-money-bill-wave"></i> Thanh toán khi nhận hàng (COD)';
        break;
      case "Chuyển khoản ngân hàng.":
        paymentHtml =
          '<i class="fas fa-university"></i> Chuyển khoản ngân hàng';
        break;
      case "Thanh toán qua thẻ.":
        paymentHtml = '<i class="fas fa-credit-card"></i> Thanh toán bằng thẻ';
        break;
    }

    document.getElementById("payment-info-display").innerHTML = paymentHtml;
  }
}
