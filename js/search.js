// Validating if the 
const SearchForm = document.getElementById("search-form");
const SearchInput = document.getElementById("search-input");
const SearchButton = document.getElementById("search-btn");
SearchButton.addEventListener("mouseover", (e) => {
    if (SearchInput.value != "" ) {
        SearchButton.style.cursor = "pointer";
    } else {
        SearchButton.style.cursor = "not-allowed";
    }
});

SearchForm.addEventListener("submit", (e) => {
    e.preventDefault();
    let searchValue = SearchForm.elements["s"].value;
    if (searchValue !== "") {
        SearchForm.submit();
    }
});

//logo navigation
const logo = document.getElementById("logo").children;
[...logo].forEach(element => {
    element.addEventListener("click", () => {
        window.location.href = "index.php";
    });
});