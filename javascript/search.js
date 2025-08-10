const search_bar = document.querySelector(".users .search input"),
  search_btn = document.querySelector(".users .search button");

search_btn.addEventListener("click", () => {
  search_bar.classList.toggle("active");
  search_bar.focus();
  search_btn.classList.toggle("active");

  if (search_btn.firstChild.classList.contains("fa-search")) {
    search_btn.firstChild.classList.replace("fa-search", "fa-xmark");
  } else {
    search_btn.firstChild.classList.replace("fa-xmark", "fa-search");
  }
});

$(search_bar).keyup(function () {
  let search_term = $(search_bar).val();

  if (search_term != "") {
    $(search_bar).addClass("active");
  } else {
    $(search_bar).removeClass("active");
    $(search_btn).removeClass("active");
    $(search_btn)
      .find(":first-child")
      .removeClass("fa-xmark")
      .addClass("fa-search");
  }
  $.ajax({
    url: "php/search.php",
    type: "POST",
    data: { search: search_term },
    contentType: "application/x-www-form-urlencoded",
    success: function (data) {
      $(".users-list").html(data);
    },
  });
}); //search bar ajax
