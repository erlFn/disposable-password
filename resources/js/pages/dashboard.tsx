import { Button } from "@/components/ui/button";
import ParentDiv from "@/layouts/parent-div";
import dashboard from "@/routes/dashboard";
import { router } from "@inertiajs/react";

interface DataType {
    email: string;
}

interface ContentProps {
    token: string
    data: DataType
}

export default function Dashboard({ data, token } : ContentProps) {

    const handleLogout = (token: string) => {
        router.post(dashboard.logout(token));
    };

    return (
        <ParentDiv>
            <p>
                Welcome, {data.email ?? 'email@example.com'}
            </p>
            <Button
                variant="destructive"
                onClick={() => handleLogout(token)}
                className="transition-all duration-250 cursor-pointer w-full"
            >
                Sign out
            </Button>
        </ParentDiv>
    );
}