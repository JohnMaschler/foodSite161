function addIngredient() {
    const ingredientsList = document.getElementById('ingredients-list');
    const newIngredient = document.createElement('div');
    newIngredient.classList.add('ingredient');
    newIngredient.innerHTML = `
        <input type="text" name="ingredient_name[]" placeholder="Ingredient name" required>
        <input type="text" name="ingredient_qty[]" placeholder="Quantity" required>
        <button type="button" onclick="removeIngredient(this)">Remove</button>
    `;
    ingredientsList.appendChild(newIngredient);
}

function removeIngredient(button) {
    button.parentElement.remove();
}
