/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.scss';
const $ = require('jquery'); 
require('bootstrap');


$(document).ready(function () {

    // Modal confirmation de suppression
    var theHREF;

    $(".confirmModalLink").click(function(e) {
        e.preventDefault();
        theHREF = $(this).attr("href");
        $("#supprProduit").modal("show");
    });

    $("#annulerSuppr").click(function(e) {
        $("#supprProduit").modal("hide");
    });

    $("#confirmerSuppr").click(function(e) {
        window.location.href = theHREF;
    });

    $(".confirmVider").click(function(e) {
        e.preventDefault();
        theHREF = $(this).attr("href");
        $("#viderPanier").modal("show");
    });

    $("#annulerVider").click(function(e) {
        $("#viderPanier").modal("hide");
    });

    $("#confirmerVider").click(function(e) {
        window.location.href = theHREF;
    });
});