var type = window.localStorage.getItem(PRODUCT_TYPE_KEY_STORAGE);
// var type = 'book';
var productJson = window.localStorage.getItem(PRODUCT_OBJECT_KEY_STORAGE);
var product = JSON.parse(productJson);
// var productJson = '{"book_id":"26","title":"68fac59ab4","description":"e93a015492dc6a3ff1d651a7c56b9f78f602970f2a22260385f9e1d2c048","price":"6000","owner":"600","writer":"d2666cf8ed","files":"[21,22,11]","images":"[11,54,26,34]","tags":"[]","created_at":"2020-07-24 14:05:54","updated_at":"2020-07-24 14:05:54"}';

var titleBox = document.getElementById('title');
var descriptionBox = document.getElementById('description');
var priceBox = document.getElementById('price');
var bigImage = document.getElementById('bigImage');
var smallImagesDiv = document.getElementById('smallImages');

if (type == 'book') {
    const { book_id, title, description, price, owner, writer, files, images, tags, created_at, updated_at } = product;
    titleBox.innerText = title;
    descriptionBox.innerText = description;
    priceBox.innerText = price;
    var imageURL = 'http://' + images[0];
    bigImage.src = imageURL;
    // var number = images.length > 4 ? 4 : images.length;
    for (let i = 0; i < images.length; i++) {
        var img = document.createElement('img');
        var imgURL = 'http://' + images[i];
        img.src = imgURL;
        img.classList.add('note1');
        img.addEventListener('click', function (n) {
            return function () {
                bigImage.src = this.src;
            };
        }(i));
        smallImagesDiv.appendChild(img);
    }
} else if (type == 'note') {
    const { note_id, title, description, price, owner, files, images, tags, created_at, updated_at } = product;
    titleBox.innerText = title;
    descriptionBox.innerText = description;
    priceBox.innerText = price;
    var imageURL = 'http://' + images[0];
    bigImage.src = imageURL;
    for (let i = 0; i < images.length; i++) {
        var img = document.createElement('img');
        var imgURL = 'http://' + images[i];
        img.src = imgURL;
        img.classList.add('note1');
        img.addEventListener('click', function (n) {
            return function () {
                bigImage.src = this.src;
            };
        }(i));
        smallImagesDiv.appendChild(img);
    }
} else if (type == 'reference') {
    const { reference_id, title, description, price, owner, files, images, tags, created_at, updated_at } = product;
    titleBox.innerText = title;
    descriptionBox.innerText = description;
    priceBox.innerText = price;
    var imageURL = 'http://' + images[0];
    bigImage.src = imageURL;
    for (let i = 0; i < images.length; i++) {
        var img = document.createElement('img');
        var imgURL = 'http://' + images[i];
        img.src = imgURL;
        img.classList.add('note1');
        img.addEventListener('click', function (n) {
            return function () {
                bigImage.src = this.src;
            };
        }(i));
        smallImagesDiv.appendChild(img);
    }
} else {
}
