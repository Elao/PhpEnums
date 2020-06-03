class Permissions extends FlaggedEnum {
  static EXECUTE = 1
  static WRITE = 2
  static READ = 4

  // Named masks
  static ALL = 7

  static get readables() {
    return {
      [Permissions.EXECUTE]: 'Execute',
      [Permissions.WRITE]: 'Write',
      [Permissions.READ]: 'Read',

      // Named masks
      [Permissions.ALL]: 'All permissions',
    };
  }
}
