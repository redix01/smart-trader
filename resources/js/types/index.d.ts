export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    avatar?: string;
    locale?: string;
    currency?: string;
    kyc_level?: string;
    account_tier?: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    flash: {
        success?: string;
        error?: string;
    };
    platform: {
        livechat_widget_code?: string;
        support_email?: string;
        support_phone?: string;
    };
    ziggy: any;
};
