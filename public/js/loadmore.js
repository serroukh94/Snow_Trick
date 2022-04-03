const loadmore = document.querySelector('#loadmore');
let currentItems = Number(loadmore.dataset.loadMoreNbElement);
let nbElement = Number(loadmore.dataset.loadMoreNbElement);
let itemSelector = loadmore.dataset.loadMoreElementSelector;

loadmore.addEventListener('click', (e) => {
    const elementList = [...document.querySelectorAll(itemSelector)];
    for (let i = currentItems; i < currentItems + nbElement; i++) {
        elementList[i].style.display = 'block';
    }
    currentItems += nbElement;

    // Load more button will be hidden after list fully loaded
    if (currentItems >= elementList.length) {
        e.target.style.display = 'none';
    }
})