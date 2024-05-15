export const useTranslations = () => {
    const translations = [
        { name: 'Privatus', value: 'Private' },
        { name: 'Viešas', value: 'Public' },
        { name: 'Nuomos užsakymas', value: 'Rent Order' },
        { name: 'Vestuvės', value: 'Wedding' },
        { name: 'Gimtadienis', value: 'Birthday' },
        { name: 'Įmonės', value: 'Corporate' },
        { name: 'Koncertas', value: 'Concert' },
        { name: 'Miesto Šventė', value: 'Town Celebration' },
        { name: 'Kalendorinė Šventė', value: 'Holiday Related' },
        { name: 'Kita', value: 'Other' },
        { name: 'Juodraštis', value: 'Draft' },
        { name: 'Planavimas', value: 'Planning' },
        { name: 'Vykdomas', value: 'In Progress' },
        { name: 'Užbaigtas', value: 'Completed' }
    ]

    const translated = {
        translation: value => translations.filter(t => t.value == value)[0].name
    }


    return translated;
}