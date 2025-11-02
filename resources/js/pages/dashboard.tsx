import ParentDiv from "@/layouts/parent-div";

interface DataType {
    email: string;
}

interface ContentProps {
    data: DataType
}

export default function Dashboard({ data } : ContentProps) {

    return (
        <ParentDiv
            isFull={true}
        >
            <p>
                Welcome, {data.email ?? 'email@example.com'}
            </p>
        </ParentDiv>
    );
}