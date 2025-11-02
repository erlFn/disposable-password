import { Spinner } from "@/components/ui/spinner";
import React from "react";

interface ContentProps {
    children: React.ReactNode;
    isLoading?: boolean;
    isFull?: boolean
}

export default function ParentDiv({ children, isLoading = false, isFull } : ContentProps)  {
    return (
        <div className="w-full min-h-screen flex flex-col items-center justify-center transition-all duration-750 opacity-100 starting:opacity-0 bg-background">
            {isLoading && (
                <div className="inset-0 z-50 absolute top-0 left-0 flex items-center justify-center bg-background/80 cursor-none">
                    <Spinner/>
                </div>
            )}
            <div className={`w-full ${isFull ? 'md:w-full' : 'md:w-xs'}`}>
                {children}
            </div>
        </div>
    );
}