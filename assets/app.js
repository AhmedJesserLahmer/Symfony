import './stimulus_bootstrap.js';
import './styles/app.css';

const wrapper = document.querySelector('.sliderWrapper');
const menuItems = document.querySelectorAll('.menuItem');

const products = [
	{
		id: 1,
		title: 'Air Force',
		price: 175,
		colors: [
			{ code: 'black', img: '/images/air.png' },
			{ code: 'darkblue', img: '/images/air2.png' },
		],
	},
	{
		id: 2,
		title: 'Air Jordan',
		price: 149,
		colors: [
			{ code: 'lightgray', img: '/images/jordan.png' },
			{ code: 'green', img: '/images/jordan2.png' },
		],
	},
	{
		id: 3,
		title: 'Blazer',
		price: 119,
		colors: [
			{ code: 'lightgray', img: '/images/blazer.png' },
			{ code: 'green', img: '/images/blazer2.png' },
		],
	},
	{
		id: 4,
		title: 'Crater',
		price: 109,
		colors: [
			{ code: 'black', img: '/images/crater.png' },
			{ code: 'lightgray', img: '/images/crater2.png' },
		],
	},
	{
		id: 5,
		title: 'Hippie',
		price: 200,
		colors: [
			{ code: 'gray', img: '/images/hippie.png' },
			{ code: 'black', img: '/images/hippie2.png' },
		],
	},
];

let chosenProduct = products[0];

const currentProductImg = document.querySelector('.productImg');
const currentProductTitle = document.querySelector('.productTitle');
const currentProductPrice = document.querySelector('.productPrice');
const currentProductColors = document.querySelectorAll('.color');
const currentProductSizes = document.querySelectorAll('.size');

if (wrapper && menuItems.length && currentProductImg && currentProductTitle && currentProductPrice) {
	menuItems.forEach((item, index) => {
		item.addEventListener('click', () => {
			wrapper.style.transform = `translateX(${-100 * index}vw)`;

			chosenProduct = products[index];
			currentProductTitle.textContent = chosenProduct.title;
			currentProductPrice.textContent = '$' + chosenProduct.price;
			currentProductImg.src = chosenProduct.colors[0].img;

			currentProductColors.forEach((color, colorIndex) => {
				if (!chosenProduct.colors[colorIndex]) {
					return;
				}
				color.style.backgroundColor = chosenProduct.colors[colorIndex].code;
			});
		});
	});
}

currentProductColors.forEach((color, index) => {
	color.addEventListener('click', () => {
		if (!chosenProduct.colors[index]) {
			return;
		}
		currentProductImg.src = chosenProduct.colors[index].img;
	});
});

currentProductSizes.forEach((size) => {
	size.addEventListener('click', () => {
		currentProductSizes.forEach((s) => {
			s.style.backgroundColor = 'white';
			s.style.color = 'black';
		});
		size.style.backgroundColor = 'black';
		size.style.color = 'white';
	});
});

const productButton = document.querySelector('.productButton');
const payment = document.querySelector('.payment');
const close = document.querySelector('.close');

if (productButton && payment && close) {
	productButton.addEventListener('click', () => {
		payment.style.display = 'flex';
	});

	close.addEventListener('click', () => {
		payment.style.display = 'none';
	});
}
