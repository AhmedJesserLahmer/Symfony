import './stimulus_bootstrap.js';
import './styles/app.css';

const initShopUI = () => {
	const wrapper = document.querySelector('.sliderWrapper');
	const menuItems = document.querySelectorAll('.menuItem');
	const sliderItems = Array.from(document.querySelectorAll('.sliderItem'));

	const sliderProducts = sliderItems.map((item) => {
		const colors = [item.dataset.color1, item.dataset.color2].filter(Boolean);
		return {
			id: item.dataset.productId || '',
			title: item.dataset.productTitle || '',
			price: item.dataset.productPrice || '',
			image: item.dataset.productImage || '',
			colors,
			cartToken: item.dataset.cartToken || '',
		};
	});

	let chosenProduct = sliderProducts[0];

	const currentProductImg = document.querySelector('.productImg');
	const currentProductTitle = document.querySelector('.productTitle');
	const currentProductPrice = document.querySelector('.productPrice');
	const currentProductColors = document.querySelectorAll('.color');
	const currentProductSizes = document.querySelectorAll('.size');

	const productBuyForm = document.querySelector('.productBuyForm');
	const productBuyToken = productBuyForm?.querySelector("input[name='_token']");
	const productBuyButton = productBuyForm?.querySelector('.productButton');
	const productActionTemplate = productBuyForm?.dataset.actionTemplate || '';

	const syncProductSection = (product) => {
		if (!product || !currentProductTitle || !currentProductPrice || !currentProductImg) {
			return;
		}
		currentProductTitle.textContent = product.title || '';
		currentProductPrice.textContent = product.price ? `${product.price} DT` : '';
		if (product.image) {
			currentProductImg.src = product.image;
		}
		currentProductColors.forEach((color, colorIndex) => {
			color.style.backgroundColor = product.colors[colorIndex] || '';
		});
		if (productBuyForm && productBuyToken && productBuyButton && product.id && product.cartToken) {
			if (productActionTemplate) {
				productBuyForm.action = productActionTemplate.replace('/0', `/${product.id}`);
			}
			productBuyToken.value = product.cartToken;
			productBuyButton.dataset.productId = product.id;
			productBuyButton.dataset.cartToken = product.cartToken;
		}
	};

	if (wrapper && menuItems.length && currentProductImg && currentProductTitle && currentProductPrice) {
		const itemCount = Math.min(menuItems.length, sliderProducts.length);
		menuItems.forEach((item, index) => {
			if (index >= itemCount) {
				return;
			}
			item.addEventListener('click', () => {
				wrapper.style.transform = `translateX(${-100 * index}vw)`;
				chosenProduct = sliderProducts[index];
				syncProductSection(chosenProduct);
			});
		});
	}

	currentProductColors.forEach((color, index) => {
		color.addEventListener('click', () => {
			if (!chosenProduct || !chosenProduct.colors[index]) {
				return;
			}
			currentProductImg.src = chosenProduct.image;
		});
	});

	currentProductSizes.forEach((size) => {
		size.addEventListener('click', () => {
			currentProductSizes.forEach((s) => {
				s.style.backgroundColor = '';
				s.style.color = '';
				s.style.borderColor = '';
			});
			size.style.backgroundColor = 'rgba(6, 182, 212, 0.2)';
			size.style.color = '#06b6d4';
			size.style.borderColor = '#06b6d4';
		});
	});


	if (sliderProducts.length) {
		syncProductSection(chosenProduct);
	}
};

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initShopUI);
} else {
	initShopUI();
}
