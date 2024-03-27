import api from './api';

const getRecipes = () => {
    return api.get('/recipes').then((response) => response.data);
}

const getRandomRecipes = (amount = 1) => {
    const query = `?quantity=${amount}`;
    return api.get('/random-recipe' + query).then((response) => response.data);
}

const getOrders = (page = 1) => {
    const query = `?page=${page}`;
    return api.get('/orders' + query).then((response) => response.data);
}

const getOrder = (id) => {
    return api.get(`/orders/${id}`).then((response) => response.data);
}

const createOrder = (order) => {
    return api.post('/orders', order).then((response) => response.data);
}

const getIngredients = () => {
    return api.get('/ingredients').then((response) => response.data);
}

const getPurchases = (page = 1, ingredientId = null) => {
    let query = `?page=${page}`;
    if (ingredientId) query += `&ingredient_id=${ingredientId}`;
    return api.get('/purchases' + query).then((response) => response.data);
}

export {
    getRecipes,
    getRandomRecipes,
    getOrders,
    getOrder,
    createOrder,
    getIngredients,
    getPurchases,
};

