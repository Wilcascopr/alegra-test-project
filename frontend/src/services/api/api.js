import axios from 'axios';

const api = axios.create({
  baseURL: `${import.meta.env.VITE_BACKEND_URL}:${import.meta.env.VITE_BACKEND_PORT}`,
});

export default api;


