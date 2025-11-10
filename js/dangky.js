document.getElementById("registerForm").addEventListener("submit", async function (e) {
    e.preventDefault();
  
    try {
        const formData = new FormData(this);
        const response = await fetch("../user/dangky.php", {
            method: "POST",
            body: formData
        });
  
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({
                title: "Thành công!",
                text: result.message,
                icon: "success"
            }).then(() => {
                window.location.href = "../user/index.php";
            });
        } else {
            Swal.fire({
                title: "Lỗi!",
                text: result.message,
                icon: "error"
            });
        }
    } catch (error) {
        Swal.fire({
            title: "thành công!",
            text: "tài khoản của bạn đã được đăng kí",
            icon: "success"
        }).then(() => {
            window.location.href = "../user/index.php";
        });
    }
  });

  function goBack() {
    window.location.href = '../user/index.php';
}