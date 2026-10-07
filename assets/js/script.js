// ---- Toast helper ----
function showToast(message) {
  const toast = document.getElementById('toast');
  if (!toast) return;
  toast.textContent = message;
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 1800);
}

// ---- Add to cart (used on product cards and product detail page) ----
document.addEventListener('click', function (e) {
  const btn = e.target.closest('.btn-add');
  if (!btn) return;

  const productId = btn.getAttribute('data-id');
  const qtyInput = document.getElementById('qty-' + productId);
  const qty = qtyInput ? parseInt(qtyInput.value, 10) || 1 : 1;

  fetch('add_to_cart.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'product_id=' + encodeURIComponent(productId) + '&qty=' + encodeURIComponent(qty)
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        const badge = document.getElementById('cartBadge');
        if (badge) badge.textContent = data.cartCount;
        showToast('Added to cart!');
      } else {
        showToast(data.message || 'Could not add to cart');
      }
    })
    .catch(() => showToast('Something went wrong. Please try again.'));
});

// ---- Cart page: quantity +/- and remove (AJAX, no page reload) ----
document.addEventListener('click', function (e) {
  const incBtn = e.target.closest('.qty-inc');
  const decBtn = e.target.closest('.qty-dec');
  const removeBtn = e.target.closest('.remove-link');

  if (incBtn || decBtn) {
    const productId = (incBtn || decBtn).getAttribute('data-id');
    const input = document.getElementById('qty-input-' + productId);
    let qty = parseInt(input.value, 10) || 1;
    qty = incBtn ? qty + 1 : Math.max(1, qty - 1);
    input.value = qty;
    updateCartQty(productId, qty);
  }

  if (removeBtn) {
    const productId = removeBtn.getAttribute('data-id');
    updateCartQty(productId, 0);
  }
});

function updateCartQty(productId, qty) {
  fetch('update_cart.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'product_id=' + encodeURIComponent(productId) + '&qty=' + encodeURIComponent(qty)
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        // Reload the page so cart totals / empty-cart message stay in sync.
        window.location.reload();
      }
    });
}
