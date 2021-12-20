var token = window.localStorage.getItem(TOKEN_KEY_SOTRAGE);
var userID = window.localStorage.getItem(USER_ID_KEY_SOTRAGE);

var imagesID = [];
var filesID = [];

window.localStorage.setItem(EDITING_MODE_KEY_STORAGE, false);

checkBuy();
searchRequestByOwner(BOOK_KEY);
searchRequestByOwner(NOTE_KEY);
getUserInfoRequest();

function checkBuy() {
    var paymentStatus = window.location.search.substr(15);
    if (paymentStatus == '1') {
        showModal(MODAL_SUCCESSFUL_MESSAGE_ID, SUCCESSFUL_PAYMENT, SUCCESSFUL_MODAL_TYPE);
    } else if (paymentStatus == '0') {
        showModal(MODAL_UNSUCCESSFUL_MESSAGE_ID, UNSUCCESSFUL_PAYMENT, UNSUCCESSFUL_MODAL_TYPE);
    }
}

function getUserInfoRequest() {
    var url = BASE_URL + SUB_URL_PROFILE + GET_MY_INFO_PAGE +
        ACCESS_TOKEN_KEY + '=' + token;

    var getUserInfo = new XMLHttpRequest();
    getUserInfo.open('GET', url);
    getUserInfo.send();

    getUserInfo.onreadystatechange = function () {
        if (getUserInfo.readyState === 4 && getUserInfo.status === 200) {
            console.log(getUserInfo.responseText);
            var response = JSON.parse(getUserInfo.responseText);
            console.log('Yes');
            if (response[CODE_KEY] === 7898) {
                console.log('Correct');
                console.log(response[MESSAGE_KEY]);
                window.localStorage.setItem(EMAIL_KEY_SOTRAGE, response[USER_KEY][EMAIL_KEY]);
                window.localStorage.setItem(MAJOR_KEY_SOTRAGE, response[USER_KEY][MAJOR_KEY]);
                window.localStorage.setItem(PURCHASES_KEY_STORAGE, response[USER_KEY][PURCHASES_KEY]);
                document.getElementById('user-name').innerHTML = response[USER_KEY][NAME_KEY];
                document.getElementById('user-email').innerHTML = response[USER_KEY][EMAIL_KEY];
                document.getElementById('name-input').value = response[USER_KEY][NAME_KEY];
                document.getElementById('email-input').value = response[USER_KEY][EMAIL_KEY];
                searchNoteById(response[USER_KEY][PURCHASES_KEY]);
            } else {
                console.log(response[CODE_KEY], response[MESSAGE_KEY]);
            }
        } else {
            console.log('There is an Erorr!!')
        }
    };
}

function addBookRequest(imagesID) {
    var title = document.getElementById('title-book').value.trim();
    var price = document.getElementById('price-book').value.trim();
    var description = document.getElementById('description-book').value.trim();
    var writer = document.getElementById('writer-book').value;

    var url = BASE_URL + SUB_URL_PROFILE + ADD_BOOK_PAGE +
        ACCESS_TOKEN_KEY + '=' + token + '&' +
        TITLE_KEY + '=' + title + '&' +
        DESCRIPTION_KEY + '=' + description + '&' +
        PRICE_KEY + '=' + price + '&' +
        WRITER_KEY + '=' + writer + '&' +
        FILES_KEY + '=' + '[]&' +
        IMAGES_KEY + '=' + JSON.stringify(imagesID) + '&' +
        TAGS_KEY + '=' + '[]';

    console.log(url);

    var addBook = new XMLHttpRequest();
    addBook.open('GET', url);
    addBook.send();

    addBook.onreadystatechange = function () {
        if (addBook.readyState === 4 && addBook.status === 200) {
            console.log(addBook.responseText);
            var response = JSON.parse(addBook.responseText);
            console.log('Yes');
            if (response[CODE_KEY] === 7898) {
                console.log('Correct');
                console.log(response[MESSAGE_KEY]);
                closeEditModal(MODAL_MAKE_BOOK_ID, BOOK_KEY);
                showModal(MODAL_SUCCESSFUL_MESSAGE_ID, SUCCESSFUL_ADD_BOOK, SUCCESSFUL_MODAL_TYPE);
                searchRequestByOwner(BOOK_KEY);
            } else {
                console.log(response[CODE_KEY], response[MESSAGE_KEY]);
                closeModal(MODAL_MAKE_BOOK_ID);
                showModal(MODAL_UNSUCCESSFUL_MESSAGE_ID, UNSUCCESSFUL_ADD_BOOK, UNSUCCESSFUL_MODAL_TYPE);
            }
        } else {
            console.log('There is an Erorr!!')
        }
    };

}

function addNoteRequest(imagesID, filesID) {
    var title = document.getElementById('title-note').value.trim();
    var price = document.getElementById('price-note').value.trim();
    var description = document.getElementById('description-note').value.trim();

    var url = BASE_URL + SUB_URL_PROFILE + ADD_NOTE_PAGE +
        ACCESS_TOKEN_KEY + '=' + token + '&' +
        TITLE_KEY + '=' + title + '&' +
        DESCRIPTION_KEY + '=' + description + '&' +
        PRICE_KEY + '=' + price + '&' +
        FILES_KEY + '=' + JSON.stringify(filesID) + '&' +
        IMAGES_KEY + '=' + JSON.stringify(imagesID) + '&' +
        TAGS_KEY + '=' + '[]';

    console.log(url);

    var addNote = new XMLHttpRequest();
    addNote.open('GET', url);
    addNote.send();

    addNote.onreadystatechange = function () {
        if (addNote.readyState === 4 && addNote.status === 200) {
            console.log(addNote.responseText);
            var response = JSON.parse(addNote.responseText);
            console.log('Yes');
            if (response[CODE_KEY] === 7898) {
                console.log('Correct');
                console.log(response[MESSAGE_KEY]);
                closeEditModal(MODAL_MAKE_NOTE_ID, NOTE_KEY);
                showModal(MODAL_SUCCESSFUL_MESSAGE_ID, SUCCESSFUL_ADD_NOTE, SUCCESSFUL_MODAL_TYPE);
                searchRequestByOwner(NOTE_KEY);
            } else {
                console.log(response[CODE_KEY], response[MESSAGE_KEY]);
                closeEditModal(MODAL_MAKE_NOTE_ID, NOTE_KEY);
                showModal(MODAL_UNSUCCESSFUL_MESSAGE_ID, UNSUCCESSFUL_ADD_NOTE, UNSUCCESSFUL_MODAL_TYPE);
            }
        } else {
            console.log('There is an Erorr!!')
        }
    };
}

async function addProduct(type) {
    var input = '';
    if (type == BOOK_KEY)
        input = document.getElementById('book-images-input');
    else
        input = document.getElementById('note-images-input');

    imagesID = [];
    for await (image of input.files) {
        sendImage(type, image);
    }
}

async function sendImage(productType, file) {
    var formData = new FormData();
    var upload = new XMLHttpRequest();
    var url = BASE_URL + SUB_URL_UPLOAD + UPLOAD_IMAGE_PAGE;
    formData.set(ACCESS_TOKEN_KEY, token);
    formData.set(IMAGE_KEY, file);
    upload.open('POST', url);
    upload.send(formData);

    upload.onreadystatechange = function () {
        if (upload.readyState === 4 && upload.status === 200) {
            console.log(upload.responseText);
            var response = JSON.parse(upload.responseText);
            console.log('Yes');
            if (response[CODE_KEY] === 7898) {
                console.log('Correct');
                makeImagesID(productType, response[IMAGE_ID_KEY]);
            } else {
                console.log(response[CODE_KEY], response[MESSAGE_KEY]);
            }
        } else {
            console.log('There is an Erorr!!')
        }
    }

}

async function sendFile(file) {
    if (file != null) {
        var formData = new FormData();
        var upload = new XMLHttpRequest();
        var url = BASE_URL + SUB_URL_UPLOAD + UPLOAD_FILE_PAGE;
        formData.set(ACCESS_TOKEN_KEY, token);
        formData.set(FILE_KEY, file);
        upload.open('POST', url);
        upload.send(formData);

        upload.onreadystatechange = function () {
            if (upload.readyState === 4 && upload.status === 200) {
                console.log(upload.responseText);
                var response = JSON.parse(upload.responseText);
                console.log('Yes');
                if (response[CODE_KEY] === 7898) {
                    console.log('Correct');
                    filesID.push(response[FILE_ID_KEY]);
                    addNoteRequest(imagesID, filesID);
                } else {
                    console.log(response[CODE_KEY], response[MESSAGE_KEY]);
                }
            } else {
                console.log('There is an Erorr!!')
            }
        }
    } else {
        showModal(MODAL_UNSUCCESSFUL_MESSAGE_ID, 'باید یک فایل برای آپلود انتخاب کنید!', UNSUCCESSFUL_MODAL_TYPE);
    }

}

function makeImagesID(productType, id) {
    var input = '';
    var inputFile = productType == NOTE_KEY ? document.getElementById('note-file-input') : null;
    if (productType == BOOK_KEY)
        input = document.getElementById('book-images-input');
    else
        input = document.getElementById('note-images-input');
    var len = input.files.length;
    console.log('len ', len, ' imagesID ', imagesID);
    if (imagesID.length < len) {
        imagesID.push(id);
    }
    if (imagesID.length == len) {
        console.log('request');
        console.log(imagesID);
        if (productType == BOOK_KEY)
            addBookRequest(imagesID);
        else
            sendFile(inputFile.files[0]);
    }
}

function searchRequestByOwner(type) {
    var search = new XMLHttpRequest();
    if (type == 'book') {
        var searchURL = BASE_URL + SUB_URL_SEARCH + SEARCH_BOOKS_PAGE +
            ACCESS_TOKEN_KEY + '=' + token + '&' + OWNER_KEY + '=' + userID;
        console.log(searchURL);
        search.onreadystatechange = function () {
            if (search.readyState === 4 && search.status === 200) {
                console.log(search.responseText);
                var response = JSON.parse(search.responseText);
                if (response[CODE_KEY] === 7898) {
                    console.log('Correct');
                    showProduct(type, response[BOOKS_KEY]);
                } else {
                    console.log(response[CODE_KEY], response[MESSAGE_KEY]);
                }
            } else {
                console.log('There is an Erorr!!')
            }
        };
        search.open('POST', searchURL);
        search.send();
    } else if (type == 'note') {
        var searchURL = BASE_URL + SUB_URL_SEARCH + SEARCH_NOTES_PAGE +
            ACCESS_TOKEN_KEY + '=' + token + '&' + OWNER_KEY + '=' + userID;
        search.onreadystatechange = function () {
            if (search.readyState === 4 && search.status === 200) {
                var response = JSON.parse(search.responseText);
                if (response[CODE_KEY] === 7898) {
                    console.log('Correct');
                    showProduct(type, response[NOTES_KEY]);
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

function showProduct(type, searchedProducts) {
    if (type == 'book') {
        var booksDiv = document.getElementById('books');
        booksDiv.innerText = '';
        if (searchedProducts.length > 0) {
            var booksSection = document.createElement('section');
            booksSection.classList.add('My_Documents');
            var sectionDiv = document.createElement('div');
            sectionDiv.classList.add('My-Document');
            for (i in searchedProducts) {
                const { book_id, created_at, description, files, images, owner, price, tags, title, updated_at, writer } = searchedProducts[i];
                var mainDiv = document.createElement('div');
                mainDiv.classList.add('item');
                var documentMy = document.createElement('div');
                documentMy.classList.add('document_my');
                var panelDiv = document.createElement('div');
                panelDiv.classList.add('document-982');
                panelDiv.classList.add('panel');
                panelDiv.classList.add('text-center');

                var topDiv = document.createElement('div');
                topDiv.classList.add('panel-heading');
                topDiv.classList.add('hover-document');
                var image = document.createElement('img');
                image.classList.add('img-responsive');
                if (images.length > 0) {
                    image.classList.add('image-document');
                    image.src = makeURL(images[0]);
                } else {
                    image.classList.add('Book');
                    image.src = 'Assets/IconBook.png';
                }
                var editDeleteDiv = document.createElement('div');
                if (images.length > 0) {
                    editDeleteDiv.classList.add('Edit_Delete');
                } else {
                    editDeleteDiv.classList.add('Edit-Delete');
                }
                var deleteDiv = document.createElement('div');
                deleteDiv.classList.add('delete_icon');
                deleteDiv.innerHTML = '<img src="Assets/IconDelete.png">';
                var editDiv = document.createElement('div');
                editDiv.classList.add('Edit_icon');
                editDiv.innerHTML = '<img src="Assets/IconEdit.png">';

                editDiv.addEventListener("click", function (n) {
                    return function () {
                        window.localStorage.setItem(EDIT_PRODUCT_KEY_STORAGE, JSON.stringify(searchedProducts[n]));
                        window.localStorage.setItem(EDITING_MODE_KEY_STORAGE, true);
                        showEditModal(BOOK_KEY);
                    };
                }(i));

                deleteDiv.addEventListener('click', () => {
                    showModal(MODAL_DELETE_DOCUMENTS_ID, '', null);
                    window.localStorage.setItem(DELETE_PRODUCT_ID_KEY_STORAGE, book_id);
                    window.localStorage.setItem(DELETE_PRODUCT_TYPE_KEY_STORAGE, BOOK_KEY);
                });

                editDeleteDiv.appendChild(deleteDiv);
                editDeleteDiv.appendChild(editDiv);
                topDiv.appendChild(image);
                topDiv.appendChild(editDeleteDiv);

                var centerDiv = document.createElement('div');
                centerDiv.classList.add('panel');
                centerDiv.classList.add('panel-body');
                centerDiv.innerHTML = title;

                var bottomDiv = document.createElement('div');
                bottomDiv.classList.add('panel-footer');
                var priceSpan = document.createElement('span');
                priceSpan.classList.add('Price');
                priceSpan.innerHTML = '<b>' + price + '</b>';
                var unitSpan = document.createElement('span');
                unitSpan.classList.add('Toman');
                unitSpan.innerHTML = 'تومان';
                bottomDiv.appendChild(priceSpan);
                bottomDiv.appendChild(unitSpan);

                panelDiv.appendChild(topDiv);
                panelDiv.appendChild(centerDiv);
                panelDiv.appendChild(bottomDiv);

                documentMy.appendChild(panelDiv);
                mainDiv.appendChild(documentMy);

                mainDiv.addEventListener("click", function (n) {
                    return function () {
                    };
                }(i));

                sectionDiv.appendChild(mainDiv);
            }
            booksSection.appendChild(sectionDiv);
            booksDiv.appendChild(booksSection);
        } else {
            booksDiv.innerHTML = '<div class="sentence animate__animated animate__fadeInDown "> کتاب دست دومی موجود نیست.</div>';
        }
    } else if (type == 'note') {
        var notesDiv = document.getElementById('notes');
        notesDiv.innerText = '';
        if (searchedProducts.length > 0) {
            var notesSection = document.createElement('section');
            notesSection.classList.add('My_Documents');
            var sectionDiv = document.createElement('div');
            sectionDiv.classList.add('My-Document');
            for (i in searchedProducts) {
                const { note_id, title, description, price, owner, files, images, tags, created_at, updated_at } = searchedProducts[i];
                var mainDiv = document.createElement('div');
                mainDiv.classList.add('item');
                var documentMy = document.createElement('div');
                documentMy.classList.add('document_my');
                var panelDiv = document.createElement('div');
                panelDiv.classList.add('document-982');
                panelDiv.classList.add('panel');
                panelDiv.classList.add('text-center');

                var topDiv = document.createElement('div');
                topDiv.classList.add('panel-heading');
                topDiv.classList.add('hover-document');
                var image = document.createElement('img');
                image.classList.add('img-responsive');
                if (images.length > 0) {
                    image.classList.add('image-document');
                    image.src = makeURL(images[0]);
                } else {
                    image.classList.add('Book');
                    image.src = 'Assets/IconNote.png';
                }
                var editDeleteDiv = document.createElement('div');
                if (images.length > 0) {
                    editDeleteDiv.classList.add('Edit_Delete');
                } else {
                    editDeleteDiv.classList.add('Edit-Delete');
                }
                var deleteDiv = document.createElement('div');
                deleteDiv.classList.add('delete_icon');
                deleteDiv.innerHTML = '<img src="Assets/IconDelete.png">';
                var editDiv = document.createElement('div');
                editDiv.classList.add('Edit_icon');
                editDiv.innerHTML = '<img src="Assets/IconEdit.png">';

                editDiv.addEventListener("click", function (n) {
                    return function () {
                        window.localStorage.setItem(EDIT_PRODUCT_KEY_STORAGE, JSON.stringify(searchedProducts[n]));
                        window.localStorage.setItem(EDITING_MODE_KEY_STORAGE, true);
                        showEditModal(NOTE_KEY);
                    };
                }(i));

                deleteDiv.addEventListener('click', () => {
                    showModal(MODAL_DELETE_DOCUMENTS_ID, '', null);
                    window.localStorage.setItem(DELETE_PRODUCT_ID_KEY_STORAGE, note_id);
                    window.localStorage.setItem(DELETE_PRODUCT_TYPE_KEY_STORAGE, NOTE_KEY);
                });

                editDeleteDiv.appendChild(deleteDiv);
                editDeleteDiv.appendChild(editDiv);
                topDiv.appendChild(image);
                topDiv.appendChild(editDeleteDiv);

                var centerDiv = document.createElement('div');
                centerDiv.classList.add('panel');
                centerDiv.classList.add('panel-body');
                centerDiv.innerHTML = title;

                var bottomDiv = document.createElement('div');
                bottomDiv.classList.add('panel-footer');
                var priceSpan = document.createElement('span');
                priceSpan.classList.add('Price');
                priceSpan.innerHTML = '<b>' + price + '</b>';
                var unitSpan = document.createElement('span');
                unitSpan.classList.add('Toman');
                unitSpan.innerHTML = 'تومان';
                bottomDiv.appendChild(priceSpan);
                bottomDiv.appendChild(unitSpan);

                panelDiv.appendChild(topDiv);
                panelDiv.appendChild(centerDiv);
                panelDiv.appendChild(bottomDiv);

                documentMy.appendChild(panelDiv);
                mainDiv.appendChild(documentMy);

                mainDiv.addEventListener("click", function (n) {
                    return function () {
                    };
                }(i));

                sectionDiv.appendChild(mainDiv);
            }
            notesSection.appendChild(sectionDiv);
            notesDiv.appendChild(notesSection);
        } else {
            notesDiv.innerHTML = '<div class="sentence animate__animated animate__fadeInDown "> کتاب دست دومی موجود نیست.</div>';
        }
    }
}

function searchNoteById(purchases) {
    console.log('purchases -> ', purchases);
    if (purchases != null && JSON.stringify(purchases) != '[]') {
        var search = new XMLHttpRequest();
        var searchURL = BASE_URL + SUB_URL_SEARCH + SEARCH_NOTES_PAGE +
            ACCESS_TOKEN_KEY + '=' + token + '&' + NOTE_IDS_KEY + '=' + JSON.stringify(purchases);
        console.log('pur url ->', searchURL);
        search.onreadystatechange = function () {
            if (search.readyState === 4 && search.status === 200) {
                console.log(search.responseText);
                var response = JSON.parse(search.responseText);
                if (response[CODE_KEY] === 7898) {
                    console.log('Correct');
                    console.log(response[NOTES_KEY]);
                    console.log(response[NOTES_KEY][0]);
                    showPurchases(response[NOTES_KEY]);
                } else {
                    console.log(response[CODE_KEY], response[MESSAGE_KEY]);
                }
            } else {
                console.log('There is an Erorr!!')
            }
        };
        search.open('POST', searchURL);
        search.send();
    } else {
        showPurchases([]);
    }
}

function showPurchases(purchasesNote) {
    var purchasesDiv = document.getElementById('purchases');
    purchasesDiv.innerText = '';
    if (purchasesNote.length > 0) {
        var firstDiv = document.createElement('div');
        firstDiv.classList.add('Document');
        firstDiv.classList.add('owl-carousel');
        firstDiv.classList.add('owl-theme');
        purchasesDiv.appendChild(firstDiv);
        for (i in purchasesNote) {
            const { note_id, title, description, price, owner, files, images, tags, created_at, updated_at } = purchasesNote[i];
            var mainDiv = document.createElement('div');
            mainDiv.classList.add('item');
            var documentBuy = document.createElement('div');
            documentBuy.classList.add('document_buy');
            var panelDiv = document.createElement('div');
            panelDiv.classList.add('panel');
            panelDiv.classList.add('text-center');

            var topDiv = document.createElement('div');
            topDiv.classList.add('panel-heading');
            var image = document.createElement('img');
            image.classList.add('img-responsive');
            if (images.length > 0) {
                image.classList.add('image-Note');
                image.src = makeURL(images[0]);
            } else {
                image.classList.add('Note');
                image.src = 'Assets/IconNote.png';
            }
            topDiv.appendChild(image);

            var bottomDiv = document.createElement('div');
            bottomDiv.classList.add('panel-footer');
            bottomDiv.innerHTML = '<p>' + title + '</p>';

            panelDiv.appendChild(topDiv);
            panelDiv.appendChild(bottomDiv);

            documentBuy.appendChild(panelDiv);
            mainDiv.appendChild(documentBuy);

            // var linkLength = http://localhost/book-shop/Codes/Back-end/files/5f31b77b203cf.jpg
            var linkLength = 47;
            mainDiv.addEventListener("click", function (n) {
                return function () {
                    download(files[0].substr(linkLength), makeURL(files[0]));
                };
            }(i));

            firstDiv.appendChild(mainDiv);
        }
    } else {
        purchasesDiv.innerHTML = '<div class="sentence animate__animated animate__fadeInDown ">جزوه‌ای خریداری نشده‌است.</div>';
    }
}

function download(filename, link) {
    var element = document.createElement('a');
    element.setAttribute('href', link);
    element.setAttribute('download', 'Note - ' + filename);
    element.style.display = 'none';
    document.body.appendChild(element);
    element.click();
    document.body.removeChild(element);
  }


function deleteProductRequest(id, type) {
    var deleteURL = BASE_URL + SUB_URL_PROFILE;
    if (type == BOOK_KEY) {
        deleteURL += DELETE_BOOK_PAGE + ACCESS_TOKEN_KEY + '=' + token + '&' +
            BOOK_ID_KEY + '=' + id;
    } else if (type == NOTE_KEY) {
        deleteURL += DELETE_NOTE_PAGE + ACCESS_TOKEN_KEY + '=' + token + '&' +
            NOTE_ID_KEY + '=' + id;
    }
    var deleteReq = new XMLHttpRequest();
    deleteReq.onreadystatechange = function () {
        if (deleteReq.readyState === 4 && deleteReq.status === 200) {
            console.log(deleteReq.responseText);
            var response = JSON.parse(deleteReq.responseText);
            if (response[CODE_KEY] === 7898) {
                closeModal(MODAL_DELETE_DOCUMENTS_ID);
                showModal(MODAL_SUCCESSFUL_MESSAGE_ID, DELETE_MESSAGE, SUCCESSFUL_MODAL_TYPE);
                type == BOOK_KEY ? searchRequestByOwner(BOOK_KEY) : searchRequestByOwner(NOTE_KEY);
            } else {
                console.log(response[CODE_KEY], response[MESSAGE_KEY]);
            }
        } else {
            console.log('There is an Erorr!!')
        }
    };
    deleteReq.open('GET', deleteURL);
    deleteReq.send();
}

function showEditModal(type) {
    var productObj = JSON.parse(window.localStorage.getItem(EDIT_PRODUCT_KEY_STORAGE));
    var productType = type == BOOK_KEY ? BOOK_KEY : NOTE_KEY;
    var id = document.getElementById('id-' + productType);
    id.value = productObj[type == BOOK_KEY ? BOOK_ID_KEY : NOTE_ID_KEY];
    var title = document.getElementById('title-' + productType);
    title.value = productObj[TITLE_KEY];
    var price = document.getElementById('price-' + productType);
    price.value = productObj[PRICE_KEY];
    var description = document.getElementById('description-' + productType);
    description.value = productObj[DESCRIPTION_KEY];
    var writer = type == BOOK_KEY ? document.getElementById('writer-' + productType) : null;
    writer != null ? writer.value = productObj[WRITER_KEY] : null;
    type == BOOK_KEY ? showModal(MODAL_MAKE_BOOK_ID, '', null) : showModal(MODAL_MAKE_NOTE_ID, '', null);
    var editingMode = window.localStorage.getItem(EDITING_MODE_KEY_STORAGE);
    var imagesDiv = document.getElementById(type + '-images-div');
    if (editingMode == 'true') document.getElementById(type + '-images-input-div').style.display = 'none';
    window.localStorage.setItem(NUMBER_OF_IMAGES_IN_EDITING_MODE_KEY_STORAGE, productObj[IMAGES_KEY].length);
    for (i in productObj[IMAGES_KEY]) {
        var mainDiv = document.createElement('div');
        mainDiv.classList.add('Add-Note');
        var img = document.createElement('img');
        img.classList.add('AddNote');
        img.src = makeURL(productObj[IMAGES_KEY][i]);
        mainDiv.appendChild(img);
        imagesDiv.appendChild(mainDiv);
    }
    document.getElementById('mode-' + productType).innerHTML = type == BOOK_KEY ? EDIT_BOOK_TITTLE : EDIT_NOTE_TITTLE;
}

function resetForm(type) {
    imagesID = [];
    filesID = [];
    var productType = type == BOOK_KEY ? BOOK_KEY : NOTE_KEY;
    var title = document.getElementById('title-' + productType);
    title.value = '';
    var price = document.getElementById('price-' + productType);
    price.value = '';
    var description = document.getElementById('description-' + productType);
    description.value = '';
    var writer = type == BOOK_KEY ? document.getElementById('writer-' + productType) : null;
    writer != null ? writer.value = '' : null;
    var images = document.getElementById(productType + '-images-input');
    images.value = '';
    if (type == NOTE_KEY)
        document.getElementById('note-file-input').value = '';
    document.getElementById('file-name').innerHTML = 'آپلود فایل جزوه';
    document.getElementById('mode-' + productType).innerHTML = type == BOOK_KEY ? ADD_BOOK_TITTLE : ADD_NOTE_TITTLE;
}

function closeEditModal(modalID, type) {
    resetForm(type);
    closeModal(modalID);
    document.getElementById(type + '-images-input-div').style.display = 'block';
    var imagesDiv = document.getElementById(type + '-images-div');
    var numberOfImages = window.localStorage.getItem(NUMBER_OF_IMAGES_IN_EDITING_MODE_KEY_STORAGE);
    var editingMode = window.localStorage.getItem(EDITING_MODE_KEY_STORAGE);
    console.log(editingMode);
    if (editingMode == 'true') {
        console.log('-->', numberOfImages);
        for (var i = 0; i < numberOfImages; i++)
            imagesDiv.removeChild(imagesDiv.lastChild);
        window.localStorage.setItem(EDITING_MODE_KEY_STORAGE, false);
    } else {
        while (imagesDiv.childNodes.length > 3)
            imagesDiv.removeChild(imagesDiv.lastChild);
    }
}

function editProductRequestButton(type) {
    var productType = type == BOOK_KEY ? BOOK_KEY : NOTE_KEY;
    var id = document.getElementById('id-' + productType).value;
    var title = document.getElementById('title-' + productType).value;
    var price = document.getElementById('price-' + productType).value;
    var description = document.getElementById('description-' + productType).value;
    var writer = type == BOOK_KEY ? document.getElementById('writer-' + productType) : null;

    var editURL = BASE_URL + SUB_URL_PROFILE;

    if (type == BOOK_KEY) {
        editURL += EDIT_BOOK_PAGE + ACCESS_TOKEN_KEY + '=' + token + '&' +
            BOOK_ID_KEY + '=' + id + '&' +
            TITLE_KEY + '=' + title + '&' +
            PRICE_KEY + '=' + price + '&' +
            DESCRIPTION_KEY + '=' + description + '&' +
            WRITER_KEY + '=' + writer;
    } else if (type == NOTE_KEY) {
        editURL += EDIT_NOTE_PAGE + ACCESS_TOKEN_KEY + '=' + token + '&' +
            NOTE_ID_KEY + '=' + id + '&' +
            TITLE_KEY + '=' + title + '&' +
            PRICE_KEY + '=' + price + '&' +
            DESCRIPTION_KEY + '=' + description;
    }

    console.log(editURL);

    var edit = new XMLHttpRequest();
    edit.open('GET', editURL);
    edit.send();

    edit.onreadystatechange = function () {
        if (edit.readyState === 4 && edit.status === 200) {
            console.log(edit.responseText);
            var response = JSON.parse(edit.responseText);
            console.log('Yes');
            if (response[CODE_KEY] === 7898) {
                console.log('Correct');
                console.log(response[MESSAGE_KEY]);
                type == BOOK_KEY ? closeModal(MODAL_MAKE_BOOK_ID) : closeModal(MODAL_MAKE_NOTE_ID);
                showModal(MODAL_SUCCESSFUL_MESSAGE_ID,
                    type == BOOK_KEY ? SUCCESSFUL_EDIT_BOOK : SUCCESSFUL_EDIT_NOTE,
                    SUCCESSFUL_MODAL_TYPE);
                window.localStorage.setItem(EDITING_MODE_KEY_STORAGE, false);
                searchRequestByOwner(type);
            } else {
                console.log(response[CODE_KEY], response[MESSAGE_KEY]);
                type == BOOK_KEY ? closeModal(MODAL_MAKE_BOOK_ID) : closeModal(MODAL_MAKE_NOTE_ID);
                showModal(MODAL_UNSUCCESSFUL_MESSAGE_ID,
                    type == BOOK_KEY ? UNSUCCESSFUL_EDIT_BOOK : UNSUCCESSFUL_EDIT_NOTE,
                    UNSUCCESSFUL_MODAL_TYPE);
                window.localStorage.setItem(EDITING_MODE_KEY_STORAGE, false);
            }
        } else {
            console.log('There is an Erorr!!')
        }
    };

}

document.getElementById('delete_product_button').addEventListener('click', () => {
    var productID = window.localStorage.getItem(DELETE_PRODUCT_ID_KEY_STORAGE);
    var productType = window.localStorage.getItem(DELETE_PRODUCT_TYPE_KEY_STORAGE);
    deleteProductRequest(productID, productType);
});

function addEditProduct(type) {
    var editingMode = window.localStorage.getItem(EDITING_MODE_KEY_STORAGE);
    editingMode == 'true' ? editProductRequestButton(type) : addProduct(type);
}

function makeScript(url) {
    if (document.getElementsByTagName('script').length < 9) {
        var script = document.createElement('script');
        script.src = url;
        script.type = 'text/javascript';
        document.body.appendChild(script);
    }
}

function makeURL(url) {
    return 'http://' + url;
}

function changePasswordRequest() {
    var password = document.getElementById('password');
    var passwordConfirmation = document.getElementById('confirm-password');

    if (password.value == passwordConfirmation.value) {

        var url = BASE_URL + SUB_URL_PROFILE + EDIT_PROFILE_PAGE +
            ACCESS_TOKEN_KEY + '=' + token + '&' +
            PASSWORD_KEY + '=' + password.value + '&' +
            PASSWORD_CONFIRMATION_KEY + '=' + passwordConfirmation.value;

        console.log(url);

        var changePassword = new XMLHttpRequest();
        changePassword.open('GET', url);
        changePassword.send();

        changePassword.onreadystatechange = function () {
            if (changePassword.readyState === 4 && changePassword.status === 200) {
                console.log(changePassword.responseText);
                var response = JSON.parse(changePassword.responseText);
                console.log('Yes');
                if (response[CODE_KEY] === 7898) {
                    console.log('Correct');
                    console.log(response[MESSAGE_KEY]);
                    closeChangePasswordModal();
                    showModal(MODAL_SUCCESSFUL_MESSAGE_ID, SUCCESSFUL_CHANGE_PASSWORD, SUCCESSFUL_MODAL_TYPE);
                } else {
                    console.log(response[CODE_KEY], response[MESSAGE_KEY]);
                    closeChangePasswordModal();
                    showModal(MODAL_UNSUCCESSFUL_MESSAGE_ID, UNSUCCESSFUL_CHANGE_PASSWORD, UNSUCCESSFUL_MODAL_TYPE);
                }
            } else {
                console.log('There is an Erorr!!')
            }
        };

    } else {
        errorMode(passwordConfirmation);
    }
}

function changeUserInfoRequest() {
    var name = document.getElementById('name-input');
    var email = document.getElementById('email-input');

    if (name.value.length < 21) {

        var url = BASE_URL + SUB_URL_PROFILE + EDIT_PROFILE_PAGE +
            ACCESS_TOKEN_KEY + '=' + token + '&' +
            NAME_KEY + '=' + name.value;

        if (email.value != window.localStorage.getItem(EMAIL_KEY_SOTRAGE))
            url += '&' + EMAIL_KEY + '=' + email.value;

        console.log(url);

        var changePassword = new XMLHttpRequest();
        changePassword.open('GET', url);
        changePassword.send();

        changePassword.onreadystatechange = function () {
            if (changePassword.readyState === 4 && changePassword.status === 200) {
                console.log(changePassword.responseText);
                var response = JSON.parse(changePassword.responseText);
                console.log('Yes');
                if (response[CODE_KEY] === 7898) {
                    console.log('Correct');
                    console.log(response[MESSAGE_KEY]);
                    closeEditProfileModal('true');
                    showModal(MODAL_SUCCESSFUL_MESSAGE_ID, SUCCESSFUL_EDIT_PROFILE, SUCCESSFUL_MODAL_TYPE);
                } else {
                    console.log(response[CODE_KEY], response[MESSAGE_KEY]);
                    closeEditProfileModal('false');
                    showModal(MODAL_UNSUCCESSFUL_MESSAGE_ID, UNSUCCESSFUL_EDIT_PROFILE, UNSUCCESSFUL_MODAL_TYPE);
                }
            } else {
                console.log('There is an Erorr!!')
            }
        };

    } else {
        errorMode(name);
    }
}

function closeChangePasswordModal() {
    closeModal(MODAL_CHANGE_PASSWORD_ID);
    document.getElementById('password').value = '';
    document.getElementById('confirm-password').value = '';
    document.getElementById('confirm-password').classList.remove('error-input');
}

function closeEditProfileModal(change) {
    closeModal(MODAL_EDIT_PROFILE_ID);
    document.getElementById('name-input').classList.remove('error-input');
    if (change == 'true') {
        console.log('1234321');
        getUserInfoRequest();
    }
}

function errorMode(input) {
    input.classList.add('error-input');
}

