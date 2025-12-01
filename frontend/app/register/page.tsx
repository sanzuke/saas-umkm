"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import Link from "next/link";
import { useAuthStore } from "@/lib/auth-store";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";

export default function RegisterPage() {
  const router = useRouter();
  const register = useAuthStore((state) => state.register);
  
  const [formData, setFormData] = useState({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    corporate_name: "",
    company_name: "",
    business_unit_name: "",
  });
  const [error, setError] = useState("");
  const [isLoading, setIsLoading] = useState(false);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setFormData(prev => ({
      ...prev,
      [e.target.name]: e.target.value
    }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError("");

    if (formData.password !== formData.password_confirmation) {
      setError("Password dan konfirmasi password tidak sama");
      return;
    }

    setIsLoading(true);

    try {
      await register(formData);
      router.push("/dashboard");
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || "Registrasi gagal. Silakan coba lagi.";
      const errors = err.response?.data?.errors;
      
      if (errors) {
        const errorList = Object.values(errors).flat().join(", ");
        setError(errorList as string);
      } else {
        setError(errorMessage);
      }
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="flex items-center justify-center min-h-screen bg-gray-50 py-8">
      <Card className="w-full max-w-2xl">
        <CardHeader className="space-y-1">
          <CardTitle className="text-2xl font-bold text-center">Daftar Akun Baru</CardTitle>
          <CardDescription className="text-center">
            Lengkapi informasi berikut untuk membuat akun
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleSubmit} className="space-y-6">
            {error && (
              <div className="p-3 text-sm text-red-500 bg-red-50 border border-red-200 rounded-md">
                {error}
              </div>
            )}

            <div className="space-y-4">
              <h3 className="font-semibold text-lg">Informasi Pengguna</h3>
              
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="space-y-2">
                  <label htmlFor="name" className="text-sm font-medium">
                    Nama Lengkap *
                  </label>
                  <Input
                    id="name"
                    name="name"
                    type="text"
                    placeholder="John Doe"
                    value={formData.name}
                    onChange={handleChange}
                    required
                    disabled={isLoading}
                  />
                </div>

                <div className="space-y-2">
                  <label htmlFor="email" className="text-sm font-medium">
                    Email *
                  </label>
                  <Input
                    id="email"
                    name="email"
                    type="email"
                    placeholder="john@perusahaan.com"
                    value={formData.email}
                    onChange={handleChange}
                    required
                    disabled={isLoading}
                  />
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="space-y-2">
                  <label htmlFor="password" className="text-sm font-medium">
                    Password *
                  </label>
                  <Input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="Minimal 8 karakter"
                    value={formData.password}
                    onChange={handleChange}
                    required
                    disabled={isLoading}
                  />
                </div>

                <div className="space-y-2">
                  <label htmlFor="password_confirmation" className="text-sm font-medium">
                    Konfirmasi Password *
                  </label>
                  <Input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    placeholder="Ulangi password"
                    value={formData.password_confirmation}
                    onChange={handleChange}
                    required
                    disabled={isLoading}
                  />
                </div>
              </div>
            </div>

            <div className="space-y-4">
              <h3 className="font-semibold text-lg">Informasi Organisasi</h3>
              
              <div className="space-y-2">
                <label htmlFor="corporate_name" className="text-sm font-medium">
                  Nama Corporate *
                </label>
                <Input
                  id="corporate_name"
                  name="corporate_name"
                  type="text"
                  placeholder="PT Contoh Indonesia"
                  value={formData.corporate_name}
                  onChange={handleChange}
                  required
                  disabled={isLoading}
                />
                <p className="text-xs text-muted-foreground">Level tertinggi organisasi Anda</p>
              </div>

              <div className="space-y-2">
                <label htmlFor="company_name" className="text-sm font-medium">
                  Nama Company *
                </label>
                <Input
                  id="company_name"
                  name="company_name"
                  type="text"
                  placeholder="Cabang Jakarta"
                  value={formData.company_name}
                  onChange={handleChange}
                  required
                  disabled={isLoading}
                />
                <p className="text-xs text-muted-foreground">Divisi atau cabang perusahaan</p>
              </div>

              <div className="space-y-2">
                <label htmlFor="business_unit_name" className="text-sm font-medium">
                  Nama Business Unit *
                </label>
                <Input
                  id="business_unit_name"
                  name="business_unit_name"
                  type="text"
                  placeholder="Toko Retail Utama"
                  value={formData.business_unit_name}
                  onChange={handleChange}
                  required
                  disabled={isLoading}
                />
                <p className="text-xs text-muted-foreground">Unit bisnis atau toko spesifik</p>
              </div>
            </div>

            <Button type="submit" className="w-full" disabled={isLoading}>
              {isLoading ? "Memproses..." : "Daftar Sekarang"}
            </Button>

            <div className="text-center text-sm">
              <span className="text-muted-foreground">Sudah punya akun? </span>
              <Link href="/login" className="text-primary hover:underline font-medium">
                Masuk di sini
              </Link>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  );
}
