import { Button } from "./button";

export function ActionIcon({
    onClick,
    children,
    variant = "default",
}: {
    onClick?: () => void;
    children: React.ReactNode;
    variant?: "default" | "danger";
}) {
    return (
        <Button
            variant="ghost"
            size="icon"
            onClick={onClick}
            className={
                variant === "danger"
                    ? "text-muted-foreground hover:text-destructive"
                    : "text-muted-foreground hover:text-primary"
            }
        >
            {children}
        </Button>
    );
}
