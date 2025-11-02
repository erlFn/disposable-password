import { PropsWithChildren } from "react";

export default function ParentDiv({ children } : PropsWithChildren)  {
    return (
        <div className="w-full min-h-screen flex flex-col items-center justify-center transition-all duration-750 opacity-100 starting:opacity-0 bg-background">
            {children}
        </div>
    );
}