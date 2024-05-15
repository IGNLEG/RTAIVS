export const useRules = () => {
    const rules = {
        required: value => !!value || 'Laukas yra privalomas.',
        email: value => {
            const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
            return pattern.test(value) || 'Neteisingas el. pašto formatas.'
        },
        phone_length: value => value.length <= 12 || 'Iki 12 simbolių.',
        password_length: value => value.length >= 8 || 'Slaptažodis turi būti bent 8 simbolių ilgio.'

    }

    return rules;
}