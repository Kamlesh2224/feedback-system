function selectCategory(id) {
    if (id == 1) {
        // Academic
        window.location.href = "academic.php?category_id=" + id;
    } 
    else if (id == 2) {
        // Infrastructure
        window.location.href = "feedback.php?category_id=" + id;
    } 
    else if (id == 3) {
        // Administrative
        window.location.href = "feedback.php?category_id=" + id;
    } 
    else if (id == 4) {
        // Bug
        window.location.href = "bug.php?category_id=" + id;
    }
}
function goToFeedback(st_id) {
    const urlParams = new URLSearchParams(window.location.search);
    const category_id = urlParams.get('category_id');

    window.location.href = "feedback.php?st_id=" + st_id + "&category_id=" + category_id;
}