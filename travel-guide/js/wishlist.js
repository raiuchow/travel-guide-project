
// ADD
function addWishlist(postId){

fetch("/api/wishlist/add.php", {
    method: "POST",
    headers: {"Content-Type":"application/json"},
    body: JSON.stringify({post_id: postId})
})
.then(res => res.json())
.then(data => {
    alert(data.message);
});
}


// REMOVE
function removeWishlist(id){

fetch("/api/wishlist/remove.php", {
    method: "DELETE",
    headers: {"Content-Type":"application/json"},
    body: JSON.stringify({id:id})
})
.then(res => res.json())
.then(data => {
    if(data.success){
        document.getElementById("item_"+id).remove();
    }
});
}