window.onload = () => {
    //Gestion des boutons "supprimer"
    let links = document.querySelectorAll("[data-delete]")

    // on boucle sur links
    for (link of links){
        // on ecoute le clic
        link.addEventListener("click", function(e){
            //on empeche la navigation
            e.preventDefault()

            //on demande confirmation
            if (confirm("Voulez-vous supprimer cette video ?")){
                // on envoie une requette Ajax vers le href du lien avec la methode DELETE
                fetch(this.getAttribute("href"), {
                    method: "DELETE",
                    headers: {
                        "X-Requested-With" : "XMLHttpRequested",
                        "Content-type": "application/json"
                    },
                    body: JSON.stringify({"_token": this.dataset.token})
                }).then(
                    //on recupere la reponse JSON
                    response => response.json()
                ).then(data => {
                    if (data.success)
                        this.parentElement.remove()
                    else
                        alert(data.error)
                }).catch(e => alert(e))
            }
        })
    }
}