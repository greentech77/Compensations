// Dev helper za generiranje fake podatkov (tipka 'f').
// Locale-specific vrednosti generira slovenianFaker; generic primitive-e
// (UUID, datumi, finance, boolean) imamo inline, da se izognemo odvisnosti
// od opuscenega "faker" paketa.
import { merge } from 'lodash'

const faker = {
    datatype: {
        uuid: () =>
            (typeof crypto !== 'undefined' && crypto.randomUUID)
                ? crypto.randomUUID()
                : 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
                    const r = (Math.random() * 16) | 0
                    const v = c === 'x' ? r : (r & 0x3) | 0x8
                    return v.toString(16)
                }),
        boolean: () => Math.random() < 0.5,
    },
    date: {
        past: () => {
            const now = Date.now()
            const oneYearMs = 365 * 24 * 60 * 60 * 1000
            return new Date(now - Math.floor(Math.random() * oneYearMs))
        },
    },
    finance: {
        amount: (min = 0, max = 1000) => {
            const value = Math.random() * (max - min) + min
            return value.toFixed(2)
        },
    },
}

const password = 'aaaaaa1!A'
const email = 'gregakop@gmail.com'
//const validEnv = process.env.MIX_APP_ENV == 'local' || process.env.MIX_APP_ENV == 'staging'

const validEnv = true

//console.log(process.env.MIX_APP_ENV);

// Slovenski podatki za faker
const slovenianData = {
    firstNames: ['Janez', 'Marija', 'Franc', 'Ana', 'Anton', 'Maja', 'Marko', 'Irena', 'Peter', 'Nina', 'Luka', 'Sara', 'Tomaž', 'Katja', 'Andrej', 'Mojca', 'Bojan', 'Tanja', 'Gregor', 'Nataša', 'Rok', 'Petra', 'Simon', 'Mojca', 'Matjaž', 'Alenka'],
    lastNames: ['Novak', 'Horvat', 'Kovačič', 'Krajnc', 'Zupančič', 'Potočnik', 'Kos', 'Vidmar', 'Golob', 'Turk', 'Petek', 'Koren', 'Zupan', 'Hribar', 'Kovač', 'Krajnc', 'Mlakar', 'Klemenčič', 'Žnidaršič', 'Kavčič', 'Medved', 'Kobal', 'Jereb', 'Korošec'],
    cities: ['Ljubljana', 'Maribor', 'Celje', 'Kranj', 'Velenje', 'Koper', 'Novo Mesto', 'Ptuj', 'Trbovlje', 'Kamnik', 'Nova Gorica', 'Jesenice', 'Murska Sobota', 'Domžale', 'Škofja Loka', 'Izola', 'Kočevje', 'Postojna', 'Logatec', 'Vrhnika', 'Slovenj Gradec', 'Krško', 'Brežice', 'Sežana'],
    streetTypes: ['Ulica', 'Cesta', 'Trg', 'Pot', 'Cesta'],
    companyNames: ['TEHNIKA', 'SISTEMI', 'SOLUTIONS', 'SERVIS', 'TRADE', 'GROUP', 'HOLDING', 'INDUSTRIJA', 'PROIZVODNJA', 'STORITVE'],
    companyTypes: ['TRGOVINA', 'PRODAJA', 'STORITVE', 'PROIZVODNJA', 'INŽENIRING', 'KONSULTING', 'LOGISTIKA', 'MARKETING'],
    companySuffixes: ['D.O.O.', 'D.D.', 'S.P.', 'S.R.O.', 'D.N.O.'],
    banks: ['Nova Ljubljanska banka', 'Banka Slovenije', 'Unicredit Banka', 'SKB Banka', 'Abanka', 'Gorenjska banka', 'Banka Intesa Sanpaolo'],
    bankBICs: ['LJUB', 'MARI', 'KOPE', 'CELJ', 'KRAN']
}

// Helper funkcije za slovenske podatke
const getRandomElement = (array) => array[Math.floor(Math.random() * array.length)]
const randomInt = (min, max) => Math.floor(Math.random() * (max - min + 1)) + min

const slovenianFaker = {
    firstName: () => getRandomElement(slovenianData.firstNames),
    lastName: () => getRandomElement(slovenianData.lastNames),
    city: () => getRandomElement(slovenianData.cities),
    companyName: () => {
        const name = getRandomElement(slovenianData.companyNames)
        const type = getRandomElement(slovenianData.companyTypes)
        const suffix = getRandomElement(slovenianData.companySuffixes)
        return `${name} ${type} ${suffix}`
    },
    streetAddress: () => {
        const streetType = getRandomElement(slovenianData.streetTypes)
        const lastName = getRandomElement(slovenianData.lastNames)
        const number = randomInt(1, 200)
        return `${streetType} ${lastName} ${number}`
    },
    phoneNumber: () => {
        const areaCodes = ['01', '02', '03', '04', '05', '07']
        const areaCode = getRandomElement(areaCodes)
        const number = randomInt(100000, 999999)
        return `${areaCode} ${number.toString().slice(0, 3)} ${number.toString().slice(3)}`
    },
    mobileNumber: () => {
        const prefix = randomInt(30, 70)
        const number = randomInt(100000, 999999)
        return `0${prefix} ${number.toString().slice(0, 3)} ${number.toString().slice(3)}`
    },
    bankName: () => getRandomElement(slovenianData.banks) + ' D.D.',
    bankBIC: () => {
        const prefix = getRandomElement(slovenianData.bankBICs)
        return `${prefix}SI2X`
    },
    email: (firstName, lastName) => {
        const domains = ['gmail.com', 'outlook.com', 'siol.net', 't-2.net', 'arnes.si']
        const name = (firstName || slovenianFaker.firstName()).toLowerCase()
        const surname = (lastName || slovenianFaker.lastName()).toLowerCase()
        const domain = getRandomElement(domains)
        const number = randomInt(1, 99)
        return `${name}.${surname}${number}@${domain}`
    }
}

/**
 * Generira fake podatke samo če so izpolnjeni določeni pogoji.
 * 
 * @param {Object} form 
 * @param {Function} generator 
 */
function bypassConditions(form, generator) {
    /*if (!validEnv || form.wasSuccessful) {
        return
    }*/
    generator(form)
}

/**
 * Mixin za fake podatke. Componenta mora implementirat fakeData metodo oz. mora mixin extendat tole.
 * Generira se jih z tipko 'f'.
 */
 const fakeMixin = {
    mounted() {
        if (validEnv) {
            document.addEventListener('keyup', this.onFakeKeyUp)
        }
    },
    unmounted() {
        if (validEnv) {
            document.removeEventListener('keyup', this.onFakeKeyUp)
        }
    },
    methods: {
        onFakeKeyUp(event) {
            if (event.key == 'f' && document.activeElement == document.body) {
                if (this.fakeData) {
                    this.fakeData()
                } else {
                    console.warn('Implementiraj fakeData metodo v komponenti.')
                }
            }
        }
    }
}

const fakeAddressString = function () {
    return slovenianFaker.streetAddress()
}

const fakeAddress = function (form) {
    form.address = fakeAddressString()
    form.postNum = randomInt(1000, 9999)
    form.postTown = slovenianFaker.city()
}

const fakeBankAccount = function(form) {
    // form.IBAN = 'SI56010000003700051'
    form.IBAN = 'SI560100' + Array.from(Array(11), () => Math.floor(Math.random() * 10)).join('')
    form.BIC = slovenianFaker.bankBIC()
    form.name = slovenianFaker.companyName()
    form.address = fakeAddressString()
}

// Function to generate a single entity
const generateEntity = () => ({
    id: faker.datatype.uuid(),
    name: slovenianFaker.companyName()
  });

const formatPercent = (value) => {
    return value.toFixed(2).replace('.', ',') + ' %';
};

  

function validTaxNumber() {
    // veljavna davčna številka
    let taxNumber = []
    for (let i = 0; i < 7; i++) {
        taxNumber.push(Math.floor(Math.random() * 9) + 1)
    }
    let sum = 0
    taxNumber.forEach((value, index) => {
        sum += parseInt(value) * (taxNumber.length - index + 1)
    });
    let x = 11 - (sum % 11)
    x = (x == 10 || x == 11) ? 0 : x
    taxNumber.push(x);
    taxNumber = parseInt(taxNumber.join(''))
    return taxNumber
}

const fakeRegisterDataMixin = (() => {
    return merge({
        methods: {
            fakeData(form) {
                form = form || this.form
                bypassConditions(form, form => {
                    const firstName = slovenianFaker.firstName()
                    const lastName = slovenianFaker.lastName()
                    
                    form.companyName = slovenianFaker.companyName()
                    form.vatNum = `SI${validTaxNumber()}`
                    form.registrationNum = `${validTaxNumber()}`
                    form.name = firstName
                    form.lastname = lastName
                    form.email = slovenianFaker.email(firstName, lastName)
                    form.phone = slovenianFaker.phoneNumber()
                    form.address = fakeAddressString()
                    form.postNum = randomInt(1000, 9999)
                    form.postTown = slovenianFaker.city()
                    form.bankAccount = 'SI560100' + Array.from(Array(11), () => Math.floor(Math.random() * 10)).join('')
                    form.bankBIC = slovenianFaker.bankBIC()
                    form.bankName = slovenianFaker.bankName()
                })
            }
        }
    }, fakeMixin)
})()


const fakeRegisterFinishMixin = (() => {
    return merge({
        methods: {
            fakeData() {
                fakeRegisterDataMixin.methods.fakeData.call(this, this.forms.entityData)
                this.form.entityData = this.forms.entityData.data()

                // da se watcher v RegisterEnterprise stabilizira
                /*this.$nextTick(() => {
                    fakeRegisterEnterpriseUBOMixin.methods.fakeData.call(this, this.forms.ubo)
                    this.form.ubo = this.forms.ubo.data()
                })*/
            }
        }
    }, fakeMixin)
})()

const fakeCompensationDataMixin = (() => {
    return merge({
      methods: {
        fakeData(form) {
          // Ensure form is the correct object
          //form = form || this.compenzationDataForm;
          form = form || this.form
            bypassConditions(form, form => {

                // Generate multiple entities (between 1-5)
                const entitiesCount = randomInt(1, 5);
                const compenzationEntities = Array.from({ length: entitiesCount }, () => ({
                    id: faker.datatype.uuid(),
                    name: slovenianFaker.companyName()
                }));

                console.log(entitiesCount);
    
                // Set form data
                form.compenzationDate = faker.date.past();
                form.compenzationAmount = faker.finance.amount(100, 10000);
                form.compenzationEntities = compenzationEntities;

                form.compenzationDiscount = Number(faker.finance.amount(1, 5));
                form.discountWithVat = faker.datatype.boolean();
                form.compenzationCommission = Number(faker.finance.amount(1, 5));
                
            })
        }
      }
    }, fakeMixin)
  })()

  const fakeCompenzationFinishMixin = (() => {
    return merge({
        methods: {
            fakeData() {
                fakeCompensationDataMixin.methods.fakeData.call(this, this.forms.compenzationData)
                this.form.compenzationData = this.forms.compenzationData.data()
            }
        }
    }, fakeMixin)
})()


export { 
    fakeRegisterDataMixin,
    fakeRegisterFinishMixin,
    fakeCompensationDataMixin,
    fakeCompenzationFinishMixin,
    fakeMixin,
};