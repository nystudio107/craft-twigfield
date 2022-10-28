type CraftTFunction = (category: string, message: string) => string;

interface Craft {
  t: CraftTFunction,

  [key: string]: any;
}
