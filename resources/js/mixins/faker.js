import * as faker from 'faker/locale/sk'; // si locale ni na voljo / sk je najbrž ok
import { merge, sample } from 'lodash'
import { fake } from 'faker';
import { isThisSecond } from 'date-fns';

const password = 'aaaaaa1!A'
const email = 'gregakop@gmail.com'
//const validEnv = process.env.MIX_APP_ENV == 'local' || process.env.MIX_APP_ENV == 'staging'

const validEnv = true

//console.log(process.env.MIX_APP_ENV);

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
    return `${faker.address.streetName()} ${Math.ceil(Math.random() + 100)}`
}

const fakeAddress = function (form) {
    form.address = fakeAddressString()
    form.postNum = 1000
    form.postTown = faker.address.city()
}

const fakeBankAccount = function(form) {
    // form.IBAN = 'SI56010000003700051'
    form.IBAN = 'SI560100' + Array.from(Array(11), () => Math.floor(Math.random() * 10)).join('')
    form.BIC = faker.finance.bic()
    form.name = faker.company.companyName()
    form.address = fakeAddressString()
}

// Function to generate a single entity
const generateEntity = () => ({
    id: faker.string.uuid(),
    name: faker.company.name()
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
                    form.companyName = faker.company.companyName()
                    form.vatNum = `SI${validTaxNumber()}`
                    form.registrationNum = `${validTaxNumber()}`
                    form.name = faker.name.firstName()
                    form.lastname = faker.name.lastName()
                    form.email = faker.internet.email()
                    form.phone = faker.phone.phoneNumber()
                    form.address = fakeAddressString()
                    form.postNum = 1000
                    form.postTown = faker.address.city()
                    form.bankAccount = 'SI560100' + Array.from(Array(11), () => Math.floor(Math.random() * 10)).join('')
                    form.bankBIC = faker.finance.bic()
                    form.bankName = faker.company.companyName()
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
                const entitiesCount = faker.datatype.number({ min: 1, max: 5 });
                const compenzationEntities = Array.from({ length: entitiesCount }, () => ({
                    id: faker.datatype.uuid(),
                    name: faker.company.companyName()
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