let modal = document.getElementById("imgModal");
let images = document.getElementsByClassName("cat-picture");
let modalImg = document.getElementById("img01");
for (let i = 0; i < images.length; i++) {
  let img = images[i];
  img.onclick = function (evt) {
    modal.style.display = "block";
    modalImg.src = this.src;
  };
}
let span = document.getElementsByClassName("close")[0];
span.onclick = function () {
  modal.style.display = "none";
};
