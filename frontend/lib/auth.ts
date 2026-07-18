import Cookies from 'js-cookie';

export const setToken = (token: string): void => {
  Cookies.set('token', token, { expires: 7, path: '/', sameSite: 'lax' });
};

export const getToken = (): string | undefined => Cookies.get('token');

export const removeToken = (): void => {
  Cookies.remove('token', { path: '/' });
};
