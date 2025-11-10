//Filter Trang sản phẩm
document.addEventListener("DOMContentLoaded", function () {
    const sortSelect = document.getElementById("sort");
    const categorySelect = document.getElementById("category");
    const productContainer = document.getElementById("product-container");
    const products = Array.from(document.querySelectorAll(".product-item"));

    function renderProducts(filteredProducts) {
        // Xóa danh sách sản phẩm cũ
        productContainer.innerHTML = "";
        // Thêm sản phẩm đã sắp xếp/lọc
        filteredProducts.forEach(product => productContainer.appendChild(product));
    }

    function filterAndSort() {
        // Lọc sản phẩm theo loại
        let filteredProducts = products.filter(product => {
            const category = categorySelect.value;
            return category === "all" || product.dataset.category === category;
        });

        // Sắp xếp sản phẩm theo giá
        const sortValue = sortSelect.value;
        if (sortValue === "price-asc") {
            filteredProducts.sort((a, b) => a.dataset.price - b.dataset.price);
        } else if (sortValue === "price-desc") {
            filteredProducts.sort((a, b) => b.dataset.price - a.dataset.price);
        }

        // Hiển thị sản phẩm
        renderProducts(filteredProducts);
    }

    // Gắn sự kiện thay đổi cho bộ lọc
    sortSelect.addEventListener("change", filterAndSort);
    categorySelect.addEventListener("change", filterAndSort);

    // Hiển thị sản phẩm ban đầu
    filterAndSort();
});
