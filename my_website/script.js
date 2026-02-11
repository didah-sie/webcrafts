
const products = [
  {
    id: 1,
    name: "Handmade Painting",
    price: 120,
    image: "https://via.placeholder.com/300x200"
  },
  {
    id: 2,
    name: "Clay Vase",
    price: 60,
    image: "https://via.placeholder.com/300x200"
  },
  {
    id: 3,
    name: "Wooden Craft",
    price: 45,
    image: "https://via.placeholder.com/300x200"
  }
];

const cart = [];

const productsContainer = document.getElementById("products");
const cartCount = document.getElementById("cart-count");
const cartItems = document.getElementById("cart-items");
const totalPrice = document.getElementById("total");
const cartModal = document.getElementById("cart-modal");

// Render products
products.forEach(product => {
  const div = document.createElement("div");
  div.className = "product";

  div.innerHTML = `
    <img src="${product.image}" alt="${product.name}" />
    <h3>${product.name}</h3>
    <p>$${product.price}</p>
    <button onclick="addToCart(${product.id})">Add to Cart</button>
  `;

  productsContainer.appendChild(div);
});

function addToCart(id) {
  const product = products.find(p => p.id === id);
  cart.push(product);
  updateCart();
}

function updateCart() {
  cartCount.textContent = cart.length;
  cartItems.innerHTML = "";
  let total = 0;

  cart.forEach(item => {
    total += item.price;
    const li = document.createElement("li");
    li.textContent = `${item.name} - $${item.price}`;
    cartItems.appendChild(li);
  });

  totalPrice.textContent = total;
}

document.getElementById("cart-btn").addEventListener("click", () => {
  cartModal.classList.toggle("hidden");
});

function closeCart() {
  cartModal.classList.add("hidden");
}

