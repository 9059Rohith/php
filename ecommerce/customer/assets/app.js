document.addEventListener('click', async (event) => {
  const button = event.target.closest('.add-cart');
  if (!button) return;
  const form = document.createElement('form');
  form.innerHTML = `<input name="action" value="add"><input name="product_id" value="${button.dataset.productId}"><input name="quantity" value="1">`;
  const csrfInput = document.querySelector('input[name="_csrf"]');
  if (csrfInput) form.innerHTML += `<input name="_csrf" value="${csrfInput.value}">`;
  try {
    const response = await fetch('/api/cart.php', { method: 'POST', body: new FormData(form) });
    const data = await response.json();
    if (data.success) {
      alert('Added to cart!');
      window.location.reload();
    } else {
      alert('Error: ' + data.message);
    }
  } catch (e) {
    console.error(e);
    alert('Failed to add to cart');
  }
});
