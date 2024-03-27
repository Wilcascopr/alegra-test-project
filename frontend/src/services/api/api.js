import axios from 'axios';
import process from 'process';

const api = axios.create({
  baseURL: process.env.BACKEND_URL || 'http://localhost:8080',
});

export default api;


