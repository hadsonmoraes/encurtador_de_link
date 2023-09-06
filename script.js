const form = document.querySelector(".wrapper form"),
    fullURL = form.querySelector("input"),
    shortenBtn = form.querySelector("form button"),
    blurEffect = document.querySelector(".blur-effect"),
    popupBox = document.querySelector(".popup-box"),
    infoBox = popupBox.querySelector(".info-box"),
    form2 = popupBox.querySelector("form"),
    shortenURL = popupBox.querySelector("form .shorten-url"),
    copyIcon = popupBox.querySelector("form .copy-icon"),
    saveBtn = popupBox.querySelector("button");

form.onsubmit = (e) => {
    e.preventDefault();
}

shortenBtn.onclick = () => {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/url-controll.php", true);
    xhr.onload = () => {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let data = xhr.response;
            if (data.length <= 5) {
                blurEffect.style.display = "block";
                popupBox.classList.add("show");

                let domain = window.location.href;
                shortenURL.value = domain + data;
                copyIcon.onclick = () => {
                    shortenURL.select();
                    document.execCommand("copy");
                }

                saveBtn.onclick = () => {
                    form2.onsubmit = (e) => {
                        e.preventDefault();
                    }

                    let xhr2 = new XMLHttpRequest();
                    xhr2.open("POST", "php/save-url.php", true);
                    xhr2.onload = () => {
                        if (xhr2.readyState == 4 && xhr2.status == 200) {
                            let data = xhr2.response;
                            if (data == "success") {
                                location.reload();
                            } else {
                                infoBox.classList.add("error");
                                infoBox.innerText = data;
                            }
                        }
                    }
                    let shorten_url1 = shortenURL.value;
                    let hidden_url = data;
                    xhr2.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xhr2.send("shorten_url=" + shorten_url1 + "&hidden_url=" + hidden_url);
                }
            } else {
                alert(data);
            }
        }
    }
    let formData = new FormData(form);
    xhr.send(formData);
}

const fileInput = document.getElementById('fileInput');
const uploadButton = document.getElementById('uploadButton');
const icon = document.querySelector("form .file-icon");
const textInput = document.getElementById('textInput');

fileInput.addEventListener('change', function () {
    // Verifique se algum arquivo foi selecionado
    if (fileInput.files.length > 0) {

        const selectedFile = fileInput.files[0];

        if (!selectedFile.name.endsWith(".xlsx") && !selectedFile.name.endsWith(".xls")) {
            alert("Por favor, selecione um arquivo Excel (.xlsx ou .xls).");

            // Limpe o valor do input para que o usuário possa selecionar outro arquivo
            fileInput.value = "";

            // Retorne da função para evitar que o código adicional seja executado
            return;
        }

        // Se um arquivo foi selecionado, mostre o botão de envio
        uploadButton.style.display = 'block';
        fileInput.style.border = '2px solid #20B2AA';
        icon.style.color = '#20B2AA';
    } else {
        // Caso contrário, oculte o botão de envio
        uploadButton.style.display = 'none';
        fileInput.style.border = '';


    }
});

textInput.addEventListener('input', function () {
    // Verifique se o campo de entrada de texto está preenchido
    if (textInput.value.trim() !== '') {
        // Se o campo de entrada de texto estiver preenchido, adicione uma borda colorida
        textInput.style.border = '2px solid #20B2AA';
    } else {
        // Caso contrário, remova a borda
        textInput.style.border = '';
    }
});





