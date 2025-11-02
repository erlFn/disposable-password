import { Alert, AlertTitle } from "@/components/ui/alert";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import ParentDiv from "@/layouts/parent-div";
import auth from "@/routes/auth";
import { SharedData } from "@/types";
import { router, useForm, usePage } from "@inertiajs/react";
import { CircleAlert, Info, Send } from "lucide-react";
import React, { useState } from "react";
import { toast } from "sonner";

interface FormTypes  {
    email: string;
}

export default function Welcome() {
    const [ isLoading, setIsLoading ] = useState(false);
    const { flash } = usePage<SharedData>().props;

    const { data, setData } = useForm<FormTypes>({
        email: ''
    });

    const handleSubmit = (e: React.FormEvent, email: string) => {
        e.preventDefault();
        setIsLoading(true);

        router.post(auth.validate(), {...data}, {
            onSuccess: () => {
                toast.success(`Password sent. Check your email - "${email}"`);
            },
            onError: (error) => {
                if (typeof error === 'string') {
                    toast.error(error);
                } else if (typeof error === 'object' && error !== null) {
                    Object.values(error).forEach(message => {
                        toast.error(message);
                    });
                } else {
                    toast.error('Unknown error occured. Please try again');
                }
            }
        });
    };

    return (
        <ParentDiv
            isLoading={isLoading}
        >
            {flash?.info && (
                <Alert className="w-full mb-8 border-blue-500/30 bg-blue-500/10 text-blue-500 flex flex-col items-center justify-center gap-2">
                    <Info/>
                    <AlertTitle className="flex flex-nowrap text-center">
                        {flash?.info}
                    </AlertTitle>
                </Alert>
            )}

            {flash?.error && (
                <Alert
                    variant="destructive"
                    className="mb-8 border-red-600/30 bg-red-500/10"
                >
                    <CircleAlert/>
                    <AlertTitle className="flex flex-nowrap text-center">
                        {flash?.error}
                    </AlertTitle>
                </Alert>
            )}
            
            <form
                onSubmit={(e) => handleSubmit(e, data.email)}
            >
                <Label
                    className="text-muted-foreground font-normal ml-0.5"
                >
                    Email:
                </Label>
                <Input
                    type="email"
                    required
                    value={data.email}
                    onChange={(e) => setData({ email: e.target.value})}
                    placeholder="email@example.com"
                    className="focus-visible:ring-0 focus-visible:border-blue-500/60 transition-all duration-250 mt-1"
                />
                <Button 
                    type="submit"
                    className="mt-4 w-full cursor-pointer transition-all duration-250"
                >
                    <Send/>
                    Send Password
                </Button>
            </form>
        </ParentDiv>
    );
}