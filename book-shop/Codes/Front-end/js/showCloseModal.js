function showModal(modalID, message = '', type) {
    var modal = document.getElementById(modalID);
    if (type != null) {
        var messageDiv = type == SUCCESSFUL_MODAL_TYPE ? 
            document.getElementById('successful_msg') : document.getElementById('unsuccessful_msg'); 
            messageDiv.innerHTML = message;
    }
    modal.style.display = 'block';
    modal.style.background = 'rgba(0,0,0,0.3)';
}

function closeModal(modalID) {
    var modal = document.getElementById(modalID);
    modal.style.display = 'none';
}