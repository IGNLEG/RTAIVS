import {toast} from "vuetify-sonner";

export const useToast = () => {
    function successToast(message: string) {
        return toast.success(message, {
            duration: 5000,
            id: 's'+Math.random().toString(36).substring(2),
            onAutoClose: () => {},
            onDismiss: () => {},
            action: {
                label: 'Uždaryti',
                onClick: () => {},
            },
            important: true,
        })
    }

    function errorToast(message: string) {
        return toast.error(message, {
            duration: 5000,
            id: 'e'+Math.random().toString(36).substring(2),
            onAutoClose: () => {},
            onDismiss: () => {},
            action: {
                label: 'Uždaryti',
                onClick: () => {},
            },
            important: true,
        })
    }


    return { successToast, errorToast }
}