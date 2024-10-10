let notifications = document.querySelector("#notification");

function createToast(type, icon, title, text) {
    // Create a unique identifier for the toast
    const toastId = `toast-${Date.now()}`;

    const toastHTML = `
        <div class="toast ${type}" id="${toastId}" style="position: relative;">
            <i class="${icon}"></i>
            <div class="content" style="padding: 10px;">
                <div class="title">${title}</div>
                <span>${text}</span>
            </div>
            <i class="fa-solid fa-xmark" onclick="document.getElementById('${toastId}').remove()"></i>
        </div>`;

    $("#notification").append(toastHTML);
    // Show the toast
    $(`#${toastId}`).toast("show");

    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        $(`#${toastId}`).fadeOut(300, () => {
            $(`#${toastId}`).remove();
        });
    }, 5000);
}
