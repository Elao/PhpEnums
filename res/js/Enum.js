/**
 * @property {String|Number} value
 *
 * @see \Elao\Enum\EnumInterface PHP
 */
class Enum {
  /**
   * @param {String|Number} value One of the possible enum values
   */
  constructor(value) {
    if (!this.constructor.accepts(value)) {
      throw new InvalidValueException(value, this.constructor);
    }

    this.value = value;
  }

  /**
   * Default implementation uses static class properties as available values
   *
   * @returns {(String|Number)[]} The list of available enum values
   */
  static get values() {
    if (this._values) {
      return this._values;
    }

    return this._values = Object.values(this).filter(v => Number.isInteger(v) || 'string' === typeof v);
  }

  /**
   * @returns {Enum[]} the list of all possible enum instances.
   */
  static get instances() {
    return this.values.map(value => new this(value));
  }

  /**
   * @param {String|Number} value
   *
   * @returns {Boolean} True if the value is an acceptable value for the enum type
   */
  static accepts(value) {
    return this.values.indexOf(value) !== -1;
  }

  /**
   * @param {String|Number} value
   *
   * @returns {Boolean} True if the enum instance has given value
   */
  is(value) {
    return value === this.value;
  }
}

/**
 * @see \Elao\Enum\ReadableEnumInterface PHP
 */
class ReadableEnum extends Enum {
  /**
   * @returns {Object<String|Number,String>} labels indexed by enumerated value
   */
  static get readables() {
    throw new Error(`A readable enum must implement "ReadableEnum.readables" (either use "static get readables()" or "static readables = {...}"). Class "${this.name}" does not.`);
  }

  /**
   * @param {String|Number} value
   *
   * @returns {String} the human readable version for this value
   */
  static readableFor(value) {
    return this.readables[value];
  }

  /**
   * @returns {String} the human readable version of the enum instance
   */
  getReadable() {
    return this.constructor.readableFor(this.value);
  }

  /**
   * @see ReadableEnum.getReadable
   */
  toString() {
    return this.getReadable();
  }
}

/**
 * @property {Number} value
 *
 * @see \Elao\Enum\FlaggedEnum PHP
 */
class FlaggedEnum extends ReadableEnum {
  static NONE = 0;

  /**
   * @type {Map<String,Number>}
   * @private
   **/
  static masks = new Map();

  /**
   * @type {Number[]}
   * @private
   **/
  flags = null;

  static get values() {
    return super.values.filter(v =>
      // Filters out any non single bit flag:
      Number.isInteger(v) && v > 0 && ((v % 2) === 0 || v === 1)
    );
  }

  /**
   * @param {Number} value
   *
   * @returns {Boolean}
   */
  static accepts(value) {
    if (!Number.isInteger(value)) {
      return false;
    }

    if (value === FlaggedEnum.NONE) {
      return true;
    }

    return value === (value & this.getBitmask());
  }

  /**
   * Gets an integer value of the possible flags for enumeration.
   *
   * @returns {Number}
   *
   * @throws Error If the possibles values are not valid bit flags
   */
  static getBitmask() {
    const enumType = this.name;

    if (!this.masks[enumType]) {
      let mask = 0;
      this.values.forEach(flag => {
        if (flag < 1 || (flag > 1 && (flag % 2) !== 0)) {
          throw new Error(`Possible value ${flag} of the enumeration "${this.name}" is not a bit flag.`);

        }
        mask |= flag;
      })

      this.masks[enumType] = mask;
    }

    return this.masks[enumType];
  }

  /**
   * Gets an array of bit flags of the value.
   *
   * @returns {Number[]} Array of individual bitflag for the current instances
   */
  getFlags() {
    if (this.flags === null) {
      this.flags = this.constructor.values.filter(flag => {
        return this.hasFlag(flag);
      });
    }

    return this.flags;
  }

  /**
   * Determines whether the specified flag is set in a numeric value.
   *
   * @param {Number} bitFlag The bit flag or bit flags
   *
   * @return {Boolean} True if the bit flag or bit flags are also set in the current instance; otherwise, false
   */
  hasFlag(bitFlag) {
    if (bitFlag >= 1) {
      return bitFlag === (bitFlag & this.value);
    }

    return false;
  }

  /**
   * Computes a new value with given flags, and returns the corresponding instance.
   *
   * @param {Number} flags The bit flag or bit flags
   *
   * @throws {InvalidValueException} When flags is not acceptable for this enumeration type
   *
   * @returns {this} The enum instance for computed value
   */
  withFlags(flags) {
    if (!this.constructor.accepts(flags)) {
      throw new InvalidValueException(flags, this.constructor);
    }

    return new this.constructor(this.value | flags);
  }

  /**
   * Computes a new value without given flags, and returns the corresponding instance.
   *
   * @param {Number} flags The bit flag or bit flags
   *
   * @throws {InvalidValueException} When flags is not acceptable for this enumeration type
   *
   * @returns {this} The enum instance for computed value
   */
  withoutFlags(flags) {
    if (!this.constructor.accepts(flags)) {
      throw new InvalidValueException(flags, this.constructor);
    }

    return new this.constructor(this.value & ~flags);
  }

  static readableForNone() {
    return 'None';
  }

  /**
   * @param {Number} value
   * @param {String} separator A delimiter used between each bit flag's readable string
   */
  static readableFor(value, separator = '; ') {
    if (!this.accepts(value)) {
      throw new InvalidValueException(value, this.name);
    }

    if (value === this.NONE) {
      return this.readableForNone();
    }

    const humanRepresentations = this.readables;

    if (humanRepresentations[value]) {
      return humanRepresentations[value];
    }

    const parts = [];

    Object.entries(humanRepresentations).forEach(([flag, readableValue]) => {
      flag = parseInt(flag);
      if (flag === (flag & value)) {
        parts.push(readableValue);
      }
    })

    return parts.join(separator);
  }

  /**
   * @param {String} separator A delimiter used between each bit flag's readable string
   *
   * @returns {String}
   */
  getReadable(separator = '; ') {
    return this.constructor.readableFor(this.value, separator);
  }
}

class InvalidValueException extends Error {
  constructor(value, enumClass) {
    super(
      `Invalid value for "${enumClass.name}" enum type. `
      + `Expected one of ${JSON.stringify(enumClass.values)}`
      + (enumClass.prototype instanceof FlaggedEnum ? ' or a valid combination of those flags' : '')
      + `. Got ${JSON.stringify(value)}.`,
    );

    this.name = 'InvalidValueException';
    this.enumClass = enumClass;
    this.value = value;
  }
}

export { ReadableEnum, FlaggedEnum, InvalidValueException };

export default Enum;
