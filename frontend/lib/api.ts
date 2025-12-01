import axios, { AxiosError, AxiosInstance } from 'axios';

const API_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api';

class ApiClient {
  private client: AxiosInstance;

  constructor() {
    this.client = axios.create({
      baseURL: API_URL,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      withCredentials: true,
    });

    // Request interceptor to add token
    this.client.interceptors.request.use(
      (config) => {
        const token = this.getToken();
        if (token) {
          config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
      },
      (error) => Promise.reject(error)
    );

    // Response interceptor for error handling
    this.client.interceptors.response.use(
      (response) => response,
      (error: AxiosError) => {
        if (error.response?.status === 401) {
          // Unauthorized - clear token and redirect to login
          this.clearToken();
          if (typeof window !== 'undefined') {
            window.location.href = '/login';
          }
        }
        return Promise.reject(error);
      }
    );
  }

  // Token management
  setToken(token: string): void {
    if (typeof window !== 'undefined') {
      localStorage.setItem('auth_token', token);
    }
  }

  getToken(): string | null {
    if (typeof window !== 'undefined') {
      return localStorage.getItem('auth_token');
    }
    return null;
  }

  clearToken(): void {
    if (typeof window !== 'undefined') {
      localStorage.removeItem('auth_token');
    }
  }

  isAuthenticated(): boolean {
    return !!this.getToken();
  }

  // Generic HTTP methods
  async get<T>(url: string, params?: any): Promise<T> {
    const response = await this.client.get<T>(url, { params });
    return response.data;
  }

  async post<T>(url: string, data?: any): Promise<T> {
    const response = await this.client.post<T>(url, data);
    return response.data;
  }

  async put<T>(url: string, data?: any): Promise<T> {
    const response = await this.client.put<T>(url, data);
    return response.data;
  }

  async delete<T>(url: string): Promise<T> {
    const response = await this.client.delete<T>(url);
    return response.data;
  }

  // Auth endpoints
  auth = {
    register: async (data: {
      name: string;
      email: string;
      password: string;
      password_confirmation: string;
      corporate_name: string;
      company_name: string;
      business_unit_name: string;
    }) => {
      const response = await this.post<any>('/register', data);
      if (response.token) {
        this.setToken(response.token);
      }
      return response;
    },

    login: async (credentials: { email: string; password: string }) => {
      const response = await this.post<any>('/login', credentials);
      if (response.token) {
        this.setToken(response.token);
      }
      return response;
    },

    logout: async () => {
      await this.post('/logout');
      this.clearToken();
    },

    me: async () => {
      return await this.get<any>('/me');
    },

    refresh: async () => {
      const response = await this.post<any>('/refresh');
      if (response.token) {
        this.setToken(response.token);
      }
      return response;
    },
  };

  // Groups endpoints
  groups = {
    list: async () => {
      return await this.get<any>('/groups');
    },

    get: async (id: number) => {
      return await this.get<any>(`/groups/${id}`);
    },

    create: async (data: {
      name: string;
      type: 'company' | 'business_unit';
      parent_id: number;
      description?: string;
    }) => {
      return await this.post<any>('/groups', data);
    },

    update: async (id: number, data: {
      name?: string;
      description?: string;
      is_active?: boolean;
    }) => {
      return await this.put<any>(`/groups/${id}`, data);
    },

    delete: async (id: number) => {
      return await this.delete<any>(`/groups/${id}`);
    },

    users: async (id: number) => {
      return await this.get<any>(`/groups/${id}/users`);
    },
  };
}

export const apiClient = new ApiClient();
export default apiClient;
