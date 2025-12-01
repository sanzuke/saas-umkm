export interface User {
  id: number;
  name: string;
  email: string;
  phone?: string;
  avatar?: string;
  tenant_id: number;
  is_active: boolean;
  is_super_admin: boolean;
  email_verified_at?: string;
  created_at: string;
  updated_at: string;
  tenant?: Group;
  groups?: GroupUser[];
}

export interface Group {
  id: number;
  name: string;
  code: string;
  type: 'corporate' | 'company' | 'business_unit';
  level: number;
  parent_id?: number;
  tenant_id: number;
  description?: string;
  is_active: boolean;
  settings?: Record<string, any>;
  created_at: string;
  updated_at: string;
  parent?: Group;
  children?: Group[];
  users?: User[];
  roles?: Role[];
}

export interface GroupUser {
  id: number;
  name: string;
  code: string;
  type: string;
  level: number;
  pivot: {
    group_id: number;
    user_id: number;
    role_ids: number[] | string;
    is_primary: boolean;
  };
}

export interface Role {
  id: number;
  name: string;
  slug: string;
  group_id: number;
  description?: string;
  is_inheritable: boolean;
  is_system: boolean;
  created_at: string;
  updated_at: string;
  permissions?: Permission[];
}

export interface Permission {
  id: number;
  name: string;
  slug: string;
  module: 'inventory' | 'pos' | 'workshop' | 'garment' | 'system';
  action: 'create' | 'read' | 'update' | 'delete' | 'manage';
  description?: string;
  is_system: boolean;
  created_at: string;
  updated_at: string;
}

export interface SubscriptionPlan {
  id: number;
  name: string;
  slug: string;
  description?: string;
  modules: string[];
  features: string[];
  price: number;
  billing_cycle: 'monthly' | 'quarterly' | 'yearly';
  max_users?: number;
  is_active: boolean;
  created_at: string;
  updated_at: string;
}

export interface Subscription {
  id: number;
  group_id: number;
  plan_id: number;
  modules_enabled: string[];
  status: 'trial' | 'active' | 'suspended' | 'cancelled' | 'expired';
  trial_ends_at?: string;
  started_at?: string;
  expires_at?: string;
  cancelled_at?: string;
  billing_info?: Record<string, any>;
  created_at: string;
  updated_at: string;
  plan?: SubscriptionPlan;
  group?: Group;
}

export interface AuthResponse {
  message: string;
  user: User;
  token: string;
  corporate?: Group;
  company?: Group;
  business_unit?: Group;
}

export interface MeResponse {
  user: User;
  permissions: string[];
  roles: string[];
}

export interface ApiError {
  message: string;
  errors?: Record<string, string[]>;
}
