class Gender extends ReadableEnum {
  static UNKNOW = 'unknown'
  static MALE = 'male'
  static FEMALE = 'female'

  static get readables() {
    return {
      [Gender.UNKNOW]: 'Unknown',
      [Gender.MALE]: 'Male',
      [Gender.FEMALE]: 'Female',
    };
  }
}
