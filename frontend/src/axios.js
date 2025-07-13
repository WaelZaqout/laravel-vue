import axios from "axios";
import router from "./router.js";

const axiosClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  withCredentials: true,
  withXSRFToken: true
});

// Request interceptor to add token to headers
axiosClient.interceptors.request.use(config => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
}, error => {
  return Promise.reject(error);
});

// Response interceptor to handle authentication errors
axiosClient.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    if (error.response && error.response.status === 401) {
      // Clear token on 401
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      
      // Redirect to login
      router.push({ name: 'Login' });
    }
    
    return Promise.reject(error);
  }
);

export default axiosClient;