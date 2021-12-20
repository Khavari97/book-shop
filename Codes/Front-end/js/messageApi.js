var accessToken = window.localStorage.getItem(TOKEN_KEY_SOTRAGE);

async function showConversations() {
    showModal(MODAL_MESSAGES_ID, '', null);
    var conversationsDiv = document.getElementById('conversations-div');
    conversationsDiv.innerHTML = '';
    var conversations = await getConversationsRequest();
    console.log(conversations);
    var counter = 0;
    for (const conv of conversations) {
        console.log(conv[PARTICIPANT_2_KEY][NAME_KEY]);
        var nameDiv = document.createElement('div');
        var imgReply = document.createElement('img');
        var messageDiv = document.createElement('div');
        nameDiv.innerHTML = conv[PARTICIPANT_2_KEY][NAME_KEY];
        nameDiv.classList.add('name_sailor');
        messageDiv.classList.add('content_message');
        imgReply.classList.add('flash');
        imgReply.src = 'Assets/IconReply.png';
        imgReply.addEventListener('click', function (n) {
            return function () {
                window.localStorage.setItem(RECEIVER_ID_KEY_STORAGE, conv[PARTICIPANT_2_KEY][USER_ID_KEY]);
                document.getElementById('contact-name').innerHTML = conv[PARTICIPANT_2_KEY][NAME_KEY];
                document.getElementById('type-message').value = '';
                showConversationMessages(conv[CONVERSATION_ID_KEY]);
                showModal(MODAL_DETAILS_MESSAGE_ID, '', null);
            };
        }(counter));
        var lastMessage = await getLastMessage(conv[CONVERSATION_ID_KEY]);
        messageDiv.innerHTML = '<p>' + lastMessage + '</p>';
        nameDiv.appendChild(imgReply);
        conversationsDiv.appendChild(nameDiv);
        conversationsDiv.appendChild(messageDiv);
        counter++;
    }
}

async function getLastMessage(conversationID) {
    var messages = await getConversationMessagesRequest(conversationID);
    return messages[messages.length - 1][CONTENT_KEY];
}

async function getConversationsRequest() {
    var getConversations = new XMLHttpRequest();
    var getConversationsURL = BASE_URL + SUB_URL_MESSAGE + GET_CONVERSATIONS_PAGE +
        ACCESS_TOKEN_KEY + '=' + accessToken;

    getConversations.open('GET', getConversationsURL, false);
    getConversations.send();

    if (getConversations.readyState === 4 && getConversations.status === 200) {
        var response = JSON.parse(getConversations.responseText);
        if (response[CODE_KEY] === 7898) {
            return response[CONVERSATIONS_KEY];
        }
    } else {

    }
}

async function getConversationMessagesRequest(conversationID) {
    var getConversationMessages = new XMLHttpRequest();
    var getConversationMessagesURL = BASE_URL + SUB_URL_MESSAGE + GET_CONVERSATION_MESSAGES_PAGE +
        ACCESS_TOKEN_KEY + '=' + accessToken + '&' + CONVERSATION_ID_KEY + '=' + conversationID;

    getConversationMessages.open('GET', getConversationMessagesURL, false);
    getConversationMessages.send();

    if (getConversationMessages.readyState === 4 && getConversationMessages.status === 200) {
        console.log(getConversationMessages.responseText);
        var response = JSON.parse(getConversationMessages.responseText);
        if (response[CODE_KEY] === 7898) {
            return response[MESSAGES_KEY];
        }
    } else {

    }
}

async function showConversationMessages(conversationID) {
    var messages = await getConversationMessagesRequest(conversationID);
    console.log(messages);
    var messagesDiv = document.getElementById('send-message-modal');
    messagesDiv.innerHTML = '';
    for (const msg of messages) {
        console.log(msg);
        var mainDiv = document.createElement('div');
        mainDiv.style.position = 'relative';
        mainDiv.style.width = '100%';
        mainDiv.style.boxSizing = 'border-box';
        var textarea = document.createElement('p');
        textarea.classList.add('send_text_message');
        textarea.style.margin = '12px';
        textarea.style.width = 'auto';
        mainDiv.style.height = '48px';
        if (msg[DIRECTION_KEY] == '0') {
            textarea.style.backgroundColor = '#AFFDFF';
            textarea.style.borderRadius = '0vw 1.5vw 1.5vw 1.5vw';
            textarea.style.position = 'absolute';
            textarea.style.left = '0';
        } else {
            textarea.style.borderRadius = '1.5vw 0 1.5vw 1.5vw';
            textarea.style.position = 'absolute';
            textarea.style.right = '0';

        }
        textarea.innerHTML = msg[CONTENT_KEY];
        mainDiv.appendChild(textarea);
        messagesDiv.appendChild(mainDiv);
    }
}

function checkHasConversation() {
    var getConversations = new XMLHttpRequest();
    var getConversationsURL = BASE_URL + SUB_URL_MESSAGE + GET_CONVERSATIONS_PAGE +
        ACCESS_TOKEN_KEY + '=' + accessToken;
    document.getElementById('type-message').value = '';
    getConversations.open('GET', getConversationsURL);
    getConversations.send();

    var productOwner = JSON.parse(window.localStorage.getItem(PRODUCT_OBJECT_KEY_STORAGE))[OWNER_KEY];
    getConversations.onreadystatechange = function () {
        if (getConversations.readyState === 4 && getConversations.status === 200) {
            var response = JSON.parse(getConversations.responseText);
            if (response[CODE_KEY] === 7898) {
                for (i in response[CONVERSATIONS_KEY]) {
                    if (productOwner == response[CONVERSATIONS_KEY][i][PARTICIPANT_1_KEY][USER_ID_KEY] || 
                        productOwner == response[CONVERSATIONS_KEY][i][PARTICIPANT_2_KEY][USER_ID_KEY]) {
                        console.log('yeh');
                        showConversationMessages(response[CONVERSATIONS_KEY][i][CONVERSATION_ID_KEY]);
                    }
                }
                showModal(MODAL_DETAILS_MESSAGE_ID, '', null);
            }
        } else {

        }
    }
}

function sendMessage(state) {
    var receiver = state == MESSAGE_IN_DOCUMENT ? JSON.parse(window.localStorage.getItem(PRODUCT_OBJECT_KEY_STORAGE))[OWNER_KEY] :
        window.localStorage.getItem(RECEIVER_ID_KEY_STORAGE);
    var messageText = document.getElementById('type-message').value;
    sendMessageRequest(receiver, messageText);
}

function sendMessageRequest(receiver, content) {
    var sendMessage = new XMLHttpRequest();
    var sendMessageURL = BASE_URL + SUB_URL_MESSAGE + SEND_MESSAGES_PAGE +
        ACCESS_TOKEN_KEY + '=' + accessToken + '&' + RECEIVER_KEY + '=' + receiver + '&' +
        CONTENT_KEY + '=' + content.trim();

    sendMessage.onreadystatechange = function () {
        if (sendMessage.readyState === 4 && sendMessage.status === 200) {
            var response = JSON.parse(sendMessage.responseText);
            if (response[CODE_KEY] === 7898) {
                console.log('Yes');
                document.getElementById('type-message').value = '';
                showMessageInModal(content);
            }
        } else {

        }
    }
    sendMessage.open('GET', sendMessageURL);
    sendMessage.send();
}

function showMessageInModal(text) {
    var sendMessageModal = document.getElementById('send-message-modal');
    var mainDiv = document.createElement('div');
    mainDiv.style.position = 'relative';
    mainDiv.style.width = '100%';
    mainDiv.style.boxSizing = 'border-box';
    var textarea = document.createElement('p');
    textarea.classList.add('send_text_message');
    textarea.style.margin = '12px';
    textarea.style.width = 'auto';
    mainDiv.style.height = '48px';
    textarea.style.borderRadius = '1.5vw 0 1.5vw 1.5vw';
    textarea.style.position = 'absolute';
    textarea.style.right = '0';
    textarea.innerHTML = text;
    mainDiv.appendChild(textarea);
    sendMessageModal.appendChild(mainDiv);
}