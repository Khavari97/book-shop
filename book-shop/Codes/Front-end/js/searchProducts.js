var titleStorage = window.localStorage.getItem(SEARCH_TITLE_KEY_STORAGE);
var typeStorage = window.localStorage.getItem(SEARCH_TYPE_KEY_STORAGE);
document.getElementById('search').value = titleStorage;

var productRadio = document.filter.product;
for (var i = 0; i < productRadio.length; i++) {
    productRadio[i].addEventListener('change', function () {
        var prevType = window.localStorage.getItem(SEARCH_TYPE_KEY_STORAGE);
        console.log(this.value);
        console.log('prev ->', prevType);
        if (this.value != prevType) {
            console.log('111');
            window.localStorage.setItem(SEARCH_TYPE_KEY_STORAGE, this.value);
            searchRequest(this.value, window.localStorage.getItem(SEARCH_TITLE_KEY_STORAGE));
        }
    });
}

if (titleStorage != null && typeStorage != null) {
    console.log(0);
    console.log(titleStorage);
    console.log(typeStorage);
    checkedProductRadio();
    searchRequest(typeStorage, titleStorage);
} else {
    console.log(titleStorage);
    console.log(typeStorage);
}

function whichProduct() {
    for (var i = 0; i < productRadio.length; i++)
        if (productRadio[i].checked)
            return productRadio[i].value;
}

function checkedProductRadio() {
    if (typeStorage == NOTE_KEY)
        productRadio[0].checked = 'checked';
    else if (typeStorage == REFERENCE_KEY)
        productRadio[1].checked = 'checked';
    else
        productRadio[2].checked = 'checked';
}


function showProduct(type, searchedProducts, size) {
    var productsDiv = document.getElementById('products');
    document.getElementById('search-number').innerHTML = size;
    productsDiv.innerText = '';
    if (searchedProducts.length > 0) {
        if (type == BOOK_KEY) {
            for (i in searchedProducts) {
                const { book_id, created_at, description, files, images, owner, price, tags, title, updated_at, writer } = searchedProducts[i];
                console.log(searchedProducts[i]);
                var mainDiv = document.createElement('div');
                mainDiv.classList.add('product');
                var sentenceDiv = document.createElement('div');
                sentenceDiv.classList.add('sentence');
                sentenceDiv.innerHTML = '<p>' + title + '</p>';
                var picDiv = document.createElement('div');
                picDiv.classList.add('picture');
                picDiv.classList.add('text-center');
                var image = document.createElement('img');
                console.log(typeof images);
                console.log(images);
                if (images.length > 0) {
                    image.src = makeURL(images[0]);
                    image.classList.add('picture_note');
                } else {
                    image.classList.add('book');
                    image.src = 'Assets/IconBook.png';
                }
                picDiv.appendChild(image);
                mainDiv.appendChild(sentenceDiv);
                mainDiv.appendChild(picDiv);
                productsDiv.appendChild(mainDiv);

                mainDiv.addEventListener("click", function (n) {
                    return function () {
                        window.localStorage.setItem(PRODUCT_TYPE_KEY_STORAGE, type);
                        window.localStorage.setItem(PRODUCT_OBJECT_KEY_STORAGE, JSON.stringify(searchedProducts[n]));
                        window.location.assign('Show-Book.html');
                    };
                }(i));
            }
        } else if (type == NOTE_KEY) {
            for (i in searchedProducts) {
                const { note_id, title, description, price, owner, files, images, tags, created_at, updated_at } = searchedProducts[i];
                console.log(searchedProducts[i]);
                var mainDiv = document.createElement('div');
                mainDiv.classList.add('product');
                var sentenceDiv = document.createElement('div');
                sentenceDiv.classList.add('sentence');
                sentenceDiv.innerHTML = '<p>' + title + '</p>';
                var picDiv = document.createElement('div');
                picDiv.classList.add('picture');
                picDiv.classList.add('text-center');
                var image = document.createElement('img');
                if (images.length > 0) {
                    image.src = makeURL(images[0]);
                    image.classList.add('picture_note');
                } else {
                    image.classList.add('book');
                    image.src = 'Assets/IconNote.png';
                }
                picDiv.appendChild(image);
                mainDiv.appendChild(sentenceDiv);
                mainDiv.appendChild(picDiv);
                productsDiv.appendChild(mainDiv);

                mainDiv.addEventListener("click", function (n) {
                    return function () {
                        window.localStorage.setItem(PRODUCT_TYPE_KEY_STORAGE, type);
                        window.localStorage.setItem(PRODUCT_OBJECT_KEY_STORAGE, JSON.stringify(searchedProducts[n]));
                        window.location.assign('Show-Note.html');
                    };
                }(i));
            }
        } else if (type == REFERENCE_KEY) {
            for (i in searchedProducts) {
                const { reference_id, title, description, price, owner, files, images, tags, created_at, updated_at } = searchedProducts[i];
                console.log(searchedProducts[i]);
                var mainDiv = document.createElement('div');
                mainDiv.classList.add('product');
                var sentenceDiv = document.createElement('div');
                sentenceDiv.classList.add('sentence');
                sentenceDiv.innerHTML = '<p>' + title + '</p>';
                var picDiv = document.createElement('div');
                picDiv.classList.add('picture');
                picDiv.classList.add('text-center');
                var image = document.createElement('img');
                if (images.length > 0) {
                    image.src = makeURL(images[0]);
                    image.classList.add('picture_note');
                } else {
                    image.classList.add('book');
                    image.src = 'Assets/IconResource.png';
                }
                picDiv.appendChild(image);
                mainDiv.appendChild(sentenceDiv);
                mainDiv.appendChild(picDiv);
                productsDiv.appendChild(mainDiv);

                mainDiv.addEventListener("click", function (n) {
                    return function () {
                        window.localStorage.setItem(PRODUCT_TYPE_KEY_STORAGE, type);
                        window.localStorage.setItem(PRODUCT_OBJECT_KEY_STORAGE, JSON.stringify(searchedProducts[n]));
                        window.location.assign('Show-Note.html');
                    };
                }(i));
            }
        }
    } else {
        if (type == BOOK_KEY)
            showModal(MODAL_UNSUCCESSFUL_MESSAGE_ID, UNSUCCESSFUL_NOT_FOUND_BOOK, UNSUCCESSFUL_MODAL_TYPE);
        else if (type == NOTE_KEY)
            showModal(MODAL_UNSUCCESSFUL_MESSAGE_ID, UNSUCCESSFUL_NOT_FOUND_NOTE, UNSUCCESSFUL_MODAL_TYPE);
        else
            showModal(MODAL_UNSUCCESSFUL_MESSAGE_ID, UNSUCCESSFUL_NOT_FOUND_REFERENCE, UNSUCCESSFUL_MODAL_TYPE);

    }
}

function searchRequest(type, title) {
    var accessToken = window.localStorage.getItem(TOKEN_KEY_SOTRAGE);
    var search = new XMLHttpRequest();
    if (type == BOOK_KEY) {
        var searchURL = BASE_URL + SUB_URL_SEARCH + SEARCH_BOOKS_PAGE +
            ACCESS_TOKEN_KEY + '=' + accessToken + '&' + TITLE_KEY + '=' + title;
        console.log(searchURL);
        search.onreadystatechange = function () {
            if (search.readyState === 4 && search.status === 200) {
                var response = JSON.parse(search.responseText);
                console.log('Yes');
                if (response[CODE_KEY] === 7898) {
                    console.log('Correct');
                    console.log(response);
                    showProduct(type, response[BOOKS_KEY], response[SIZE_KEY]);
                } else {
                    console.log(response[CODE_KEY], response[MESSAGE_KEY]);
                }
            } else {
                console.log('There is an Erorr!!')
            }
        };
        search.open('POST', searchURL);
        search.send();
    } else if (type == NOTE_KEY) {
        var searchURL = BASE_URL + SUB_URL_SEARCH + SEARCH_NOTES_PAGE +
            ACCESS_TOKEN_KEY + '=' + accessToken + '&' + TITLE_KEY + '=' + title;
        search.onreadystatechange = function () {
            if (search.readyState === 4 && search.status === 200) {
                var response = JSON.parse(search.responseText);
                if (response[CODE_KEY] === 7898) {
                    console.log('Correct');
                    console.log(response);
                    showProduct(type, response[NOTES_KEY], response[SIZE_KEY]);
                } else {
                    console.log(response[CODE_KEY], response[MESSAGE_KEY]);
                }
            } else {
                console.log('There is an Erorr!!')
            }
        };
        search.open('POST', searchURL);
        search.send();
    } else if (type == REFERENCE_KEY) {
        var searchURL = BASE_URL + SUB_URL_SEARCH + SEARCH_REFERENCES_PAGE +
            ACCESS_TOKEN_KEY + '=' + accessToken + '&' + TITLE_KEY + '=' + title;
        search.onreadystatechange = function () {
            if (search.readyState === 4 && search.status === 200) {
                var response = JSON.parse(search.responseText);
                if (response[CODE_KEY] === 7898) {
                    console.log('Correct');
                    console.log(response);
                    showProduct(type, response[REFERENCES_KEY], response[SIZE_KEY]);
                } else {
                    console.log(response[CODE_KEY], response[MESSAGE_KEY]);
                }
            } else {
                console.log('There is an Erorr!!')
            }
        };
        search.open('POST', searchURL);
        search.send();
    }
}

function searchButton() {
    var title = document.getElementById('search').value;
    var type = whichProduct();
    console.log(title);
    window.localStorage.setItem(SEARCH_TITLE_KEY_STORAGE, title);
    window.localStorage.setItem(SEARCH_TYPE_KEY_STORAGE, type);
    searchRequest(type, title);
    console.log(1);
}

function makeURL(url) {
    return 'http://' + url;
}

