function ShowProduitModal(id) {
    Livewire.dispatch("setPostId", { id: id });
    $("#product-view").modal("toggle");
}


function sweet_alert(titre, type, text, duree) {
    Swal.fire({
        position: "center",
        icon: type,
        title: titre,
        text: text,
        showConfirmButton: false,
        timer: duree,
    });
}
function AddToCart(id, shopId) {
    var quantityElement = $("#qte-" + id);
    var quantity = quantityElement.length ? quantityElement.val() : 1;

    var data = {
        id_produit: id,
        quantite: quantity,
        shop_id: shopId
    };

    fetch("/client/ajouter_au_panier", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
        },
        body: JSON.stringify(data),
    })
        .then(async (response) => {
            // On récupère le JSON, qu'il y ait une erreur 400 ou une réussite 200
            const result = await response.json();

            if (response.ok) {
                // Cas de réussite (200)
                get_panier();
                sweet_alert("Félicitation", "success", result.message, 1500);
            } else {
                // Cas d'erreur (400, 404, etc.) - On affiche le message du serveur proprement
                sweet_alert("Attention !", "warning", data.message, 2500);
            }
        })
        .catch((error) => {
            // Ce bloc ne sera appelé que si le serveur ne renvoie pas du JSON du tout
            console.error("Erreur critique :", error);
        });
}





function DeleteToCart(id, shopId) {
    $.post(
        "/client/delete_produit_au_panier",
        {
            _token: $('meta[name="csrf-token"]').attr('content'), // Indispensable pour POST
            id_produit: id,
            shop_id: shopId
        },
        function (data) {
            if (data.statut) {
                get_panier();
            } else {
                console.log("Erreur lors de la suppression :", data.message);
            }
        }
    ).fail(function (xhr) {
        console.error("Erreur 404 : Vérifiez que la route /client/delete_produit_au_panier existe bien en POST dans web.php");
    });
}

get_panier();

function get_panier() {
    $.get("/client/count_panier", function (data, status) {
        if (status === 'success') {
            console.log(data.list);
            $("#count-panier-span").text(data.total);
            /*    $("#list_content_panier").html(data.list);   */
            $("#list_content_panier").html(data.html);
            $("#montant_total_panier").html(data.montant_total + " XCFA");
        } else {
            console.log("error get panier");
        }
    });
}
//////////////////FAVORIS/////////////////////////////////////////
function AddFavoris(id) {
    var csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");
    var data = {
        id_produit: id,
        _token: csrfToken,
    };
    //  var icon = document.querySelector(`#favori-icon-${id}`);
    fetch("/client/ajouter_favoris", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
        },
        body: JSON.stringify(data),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.statut) {
                sweet_alert("Félicitation", "success", data.message, 1500);
            } else {
                console.log("Erreur lors de l'ajout du produit  aux favoris .");
            }
        })
        .catch((error) => {
            console.error("Erreur:", error);
        });


}





$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

/////////////SHOP///////////////////////////////////////////////
function applyFilter(select) {
    const url = new URL(window.location.href);
    const value = select.value;

    url.searchParams.delete('highlight');

    if (value === 'promo' || value === 'is_new' || value === 'best' || value === 'most_viewed') {
        url.searchParams.set('highlight', value);
    }

    window.location.href = url.toString();
}

///////////////////////TESTIMONIALS///////////////////////////////////////
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById('testimonialForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const modalElement = document.getElementById('exampleModal');
            const formData = new FormData(this);

            fetch(this.getAttribute('action'), {
                method: this.getAttribute('method'),
                body: new URLSearchParams(formData),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(async response => {
                    if (response.ok) {
                        return response.json().catch(() => ({}));
                    } else {
                        const data = await response.json().catch(() => ({}));
                        throw data;
                    }
                })
                .then(data => {
                    if (modalElement && typeof bootstrap !== 'undefined') {
                        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
                        modalInstance.hide();
                    }

                    const successText = document.getElementById('successMessageText').value;
                    document.getElementById('successMessage').innerText = successText;
                    document.getElementById('successMessage').style.display = 'block';
                    document.getElementById('errorMessage').style.display = 'none';
                    form.reset();

                    setTimeout(function () {
                        location.reload();
                    }, 2000);
                })
                .catch(error => {
                    let errorMsg = document.getElementById('errorMessageText').value;

                    if (error && error.errors) {
                        let messages = [];
                        for (let key in error.errors) {
                            messages.push(error.errors[key][0]);
                        }
                        if (messages.length > 0) {
                            errorMsg = messages.join('<br>');
                        }
                    } else if (error && error.message) {
                        errorMsg = error.message;
                    }

                    const errorDiv = document.getElementById('errorMessage');
                    errorDiv.innerHTML = errorMsg;
                    errorDiv.style.display = 'block';
                    document.getElementById('successMessage').style.display = 'none';
                });
        });
    }
});


