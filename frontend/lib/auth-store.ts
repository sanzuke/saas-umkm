import { create } from 'zustand';
import { User, MeResponse } from '@/types';
import { apiClient } from '@/lib/api';

interface AuthState {
  user: User | null;
  permissions: string[];
  roles: string[];
  isAuthenticated: boolean;
  isLoading: boolean;
  setUser: (user: User | null) => void;
  setAuth: (data: MeResponse) => void;
  login: (email: string, password: string) => Promise<void>;
  register: (data: any) => Promise<void>;
  logout: () => Promise<void>;
  fetchUser: () => Promise<void>;
  checkAuth: () => boolean;
  hasPermission: (permission: string) => boolean;
  hasRole: (role: string) => boolean;
}

export const useAuthStore = create<AuthState>((set, get) => ({
  user: null,
  permissions: [],
  roles: [],
  isAuthenticated: false,
  isLoading: true,

  setUser: (user) => set({ user, isAuthenticated: !!user }),

  setAuth: (data) => set({
    user: data.user,
    permissions: data.permissions,
    roles: data.roles,
    isAuthenticated: true,
    isLoading: false,
  }),

  login: async (email, password) => {
    try {
      const response = await apiClient.auth.login({ email, password });
      const meData = await apiClient.auth.me();
      get().setAuth(meData);
    } catch (error) {
      set({ isLoading: false });
      throw error;
    }
  },

  register: async (data) => {
    try {
      const response = await apiClient.auth.register(data);
      const meData = await apiClient.auth.me();
      get().setAuth(meData);
    } catch (error) {
      set({ isLoading: false });
      throw error;
    }
  },

  logout: async () => {
    try {
      await apiClient.auth.logout();
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      set({
        user: null,
        permissions: [],
        roles: [],
        isAuthenticated: false,
      });
    }
  },

  fetchUser: async () => {
    try {
      set({ isLoading: true });
      const data = await apiClient.auth.me();
      get().setAuth(data);
    } catch (error) {
      set({
        user: null,
        permissions: [],
        roles: [],
        isAuthenticated: false,
        isLoading: false,
      });
    }
  },

  checkAuth: () => {
    const token = apiClient.getToken();
    if (token && !get().user) {
      get().fetchUser();
      return true;
    }
    return !!get().user;
  },

  hasPermission: (permission) => {
    const { permissions, user } = get();
    if (user?.is_super_admin) return true;
    return permissions.includes(permission);
  },

  hasRole: (role) => {
    const { roles, user } = get();
    if (user?.is_super_admin) return true;
    return roles.includes(role);
  },
}));
