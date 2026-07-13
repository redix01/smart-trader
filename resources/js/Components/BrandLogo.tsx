import { usePage } from '@inertiajs/react';
import { PageProps } from '@/types';

interface BrandLogoProps {
  className?: string;
  alt?: string;
}

export default function BrandLogo({ className = '', alt }: BrandLogoProps) {
  const { platform } = usePage<PageProps>();
  const brand = alt ?? platform.site_name;
  return (
    <img
      src="/img/logo.png"
      alt={brand}
      className={className}
    />
  );
}
