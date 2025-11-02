import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import ParentDiv from "@/layouts/parent-div";
import auth from "@/routes/auth";
import { router, useForm } from "@inertiajs/react";
import { Send } from "lucide-react";
import React, { useState } from "react";
import { toast } from "sonner";

interface FormType {
    password: string;
}

interface ContentProps {
    token: string;
}

export default function Verify({ token } : ContentProps) {
    const [ isLoading, setIsLoading ] = useState(false);

    const { data, setData } = useForm<FormType>({
        password: ''
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setIsLoading(true);
        
        router.post(auth.verify(token), {...data}, {
            onSuccess: () => {
                toast.success('Success! this URL is valid for 1 hour.');
            }
        });
    };

    return (
        <ParentDiv
            isLoading={isLoading}
        >
            <form
                onSubmit={handleSubmit}
            >
                <Label
                    className="text-muted-foreground font-normal ml-0.5"
                >
                    Password:
                </Label>
                <Input
                    value={data.password}
                    onChange={(e) => setData({ password: e.target.value })}
                    className="focus-visible:ring-0 focus-visible:border-blue-500/60 transition-all duration-250 mt-1"
                />
                <Button 
                    type="submit"
                    className="mt-4 w-full cursor-pointer transition-all duration-250"
                >
                    <Send/>
                    Verify Password
                </Button>
            </form>
        </ParentDiv>
    );
}