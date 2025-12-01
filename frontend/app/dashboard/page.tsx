"use client";

import { useAuthStore } from "@/lib/auth-store";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Building2, Users, Package, TrendingUp } from "lucide-react";

export default function DashboardPage() {
  const { user } = useAuthStore();

  const stats = [
    {
      title: "Total Organisasi",
      value: "3",
      description: "Corporate, Company, Business Unit",
      icon: Building2,
      color: "text-blue-600",
      bgColor: "bg-blue-50",
    },
    {
      title: "Total Pengguna",
      value: user?.groups?.length.toString() || "0",
      description: "Pengguna aktif",
      icon: Users,
      color: "text-green-600",
      bgColor: "bg-green-50",
    },
    {
      title: "Modul Aktif",
      value: "4",
      description: "Inventory, POS, Bengkel, Konveksi",
      icon: Package,
      color: "text-purple-600",
      bgColor: "bg-purple-50",
    },
    {
      title: "Status",
      value: "Aktif",
      description: "Semua sistem berjalan normal",
      icon: TrendingUp,
      color: "text-orange-600",
      bgColor: "bg-orange-50",
    },
  ];

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p className="text-muted-foreground mt-1">
          Selamat datang, {user?.name}! Berikut adalah ringkasan bisnis Anda.
        </p>
      </div>

      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {stats.map((stat, index) => {
          const Icon = stat.icon;
          return (
            <Card key={index}>
              <CardHeader className="flex flex-row items-center justify-between pb-2">
                <CardTitle className="text-sm font-medium text-muted-foreground">
                  {stat.title}
                </CardTitle>
                <div className={`p-2 rounded-lg ${stat.bgColor}`}>
                  <Icon className={`h-5 w-5 ${stat.color}`} />
                </div>
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">{stat.value}</div>
                <p className="text-xs text-muted-foreground mt-1">
                  {stat.description}
                </p>
              </CardContent>
            </Card>
          );
        })}
      </div>

      {/* Organization Overview */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <Card>
          <CardHeader>
            <CardTitle>Informasi Organisasi</CardTitle>
            <CardDescription>
              Struktur hierarki organisasi Anda
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              <div className="flex items-start gap-3">
                <div className="p-2 bg-blue-50 rounded-lg">
                  <Building2 className="h-5 w-5 text-blue-600" />
                </div>
                <div className="flex-1">
                  <p className="font-medium">Corporate</p>
                  <p className="text-sm text-muted-foreground">
                    {user?.tenant?.name || "Loading..."}
                  </p>
                </div>
              </div>

              {user?.groups && user.groups.length > 0 && (
                <div className="flex items-start gap-3">
                  <div className="p-2 bg-green-50 rounded-lg">
                    <Building2 className="h-5 w-5 text-green-600" />
                  </div>
                  <div className="flex-1">
                    <p className="font-medium">Business Units</p>
                    <p className="text-sm text-muted-foreground">
                      {user.groups.map(g => g.name).join(", ")}
                    </p>
                  </div>
                </div>
              )}

              <div className="flex items-start gap-3">
                <div className="p-2 bg-purple-50 rounded-lg">
                  <Users className="h-5 w-5 text-purple-600" />
                </div>
                <div className="flex-1">
                  <p className="font-medium">Role Anda</p>
                  <p className="text-sm text-muted-foreground">
                    {user?.is_super_admin ? "Super Administrator" : "User"}
                  </p>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Modul Tersedia</CardTitle>
            <CardDescription>
              Sistem yang dapat Anda akses
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-3">
              {[
                { name: "Inventory Management", status: "Aktif", color: "bg-green-500" },
                { name: "POS Retail", status: "Aktif", color: "bg-green-500" },
                { name: "Manajemen Bengkel", status: "Aktif", color: "bg-green-500" },
                { name: "Manajemen Konveksi", status: "Aktif", color: "bg-green-500" },
              ].map((module, index) => (
                <div key={index} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                  <span className="text-sm font-medium">{module.name}</span>
                  <div className="flex items-center gap-2">
                    <div className={`w-2 h-2 rounded-full ${module.color}`} />
                    <span className="text-xs text-muted-foreground">{module.status}</span>
                  </div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Quick Actions */}
      <Card>
        <CardHeader>
          <CardTitle>Panduan Cepat</CardTitle>
          <CardDescription>
            Langkah selanjutnya untuk memulai
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div className="p-4 border rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
              <h4 className="font-medium mb-2">1. Tambah Pengguna</h4>
              <p className="text-sm text-muted-foreground">
                Undang anggota tim Anda untuk bergabung
              </p>
            </div>
            <div className="p-4 border rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
              <h4 className="font-medium mb-2">2. Atur Organisasi</h4>
              <p className="text-sm text-muted-foreground">
                Kelola struktur dan unit bisnis Anda
              </p>
            </div>
            <div className="p-4 border rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
              <h4 className="font-medium mb-2">3. Mulai Transaksi</h4>
              <p className="text-sm text-muted-foreground">
                Akses modul untuk memulai operasional
              </p>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
